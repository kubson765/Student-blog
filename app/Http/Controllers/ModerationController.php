<?php

namespace App\Http\Controllers;

use App\Models\AnonymousPost;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ModerationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'not-banned']);
        $this->middleware('can:moderate,App\Models\Post');
        $this->middleware('throttle:moderation');
    }

    /**
     * Kolejka moderacji
     */
    public function index(Request $request)
    {
        $type = $request->input('type', 'anonymous');

        $items = match ($type) {
            'posts' => Post::where('moderation_status', 'pending')
                ->with('user')
                ->oldest()
                ->paginate(20),
            'comments' => Comment::where('moderation_status', 'pending')
                ->with(['user', 'post'])
                ->oldest()
                ->paginate(20),
            default => AnonymousPost::where('moderation_status', 'pending')
                ->oldest()
                ->paginate(20),
        };

        $stats = [
            'pending_anonymous' => AnonymousPost::where('moderation_status', 'pending')->count(),
            'pending_posts' => Post::where('moderation_status', 'pending')->count(),
            'pending_reports' => \App\Models\Report::where('status', 'pending')->count(),
            'pending_comments' => Comment::where('moderation_status', 'pending')->count(),
        ];

        return view('moderation.index', compact('items', 'type', 'stats'));
    }

    /**
     * Szczegóły
     */
    public function show(string $type, int $id)
    {
        $post = $this->resolvePost($type, $id);

        return view('moderation.show', compact('post', 'type'));
    }

    /**
     * Akceptacja
     */
    public function approve(Request $request, string $type, int $id)
    {
        $post = $this->resolvePost($type, $id);
        $this->authorize('approve', $post);

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($post, $request, $type) {
            $data = [
                'moderation_status' => 'approved',
                'moderation_reason' => $request->reason,
                'moderated_at' => now(),
                'moderated_by' => auth()->id(),
            ];

            if (in_array($type, ['posts', 'anonymous'])) {
                $data['published_at'] = $post->published_at ?? now();
            }

            $post->update($data);

            $post->logModerationAction(auth()->user(), 'approve', $request->reason);
        });

        return redirect()
            ->route('moderation.index', ['type' => $type])
            ->with('success', 'Zaakceptowano.');
    }

    /**
     * Odrzucenie
     */
    public function reject(Request $request, string $type, int $id)
    {
        $post = $this->resolvePost($type, $id);
        $this->authorize('reject', $post);

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        DB::transaction(function () use ($post, $request) {
            $post->update([
                'moderation_status' => 'rejected',
                'moderation_reason' => $request->reason,
                'moderated_at' => now(),
                'moderated_by' => auth()->id(),
            ]);

            $post->logModerationAction(auth()->user(), 'reject', $request->reason);
        });

        return redirect()
            ->route('moderation.index', ['type' => $type])
            ->with('success', 'Post odrzucony.');
    }

    /**
     * Lista zgłoszeń dla moderatora
     */
    public function reports(Request $request)
    {
        $query = Report::with(['reporter', 'reportable'])
            ->where('status', 'pending')
            ->latest();

        // Filtr po statusie
        if ($status = $request->input('status', 'pending')) {
            $query->where('status', $status);
        }

        // Filtr po powodzie
        if ($reason = $request->input('reason')) {
            $query->where('reason', $reason);
        }

        $reports = $query->paginate(20)->withQueryString();

        // Statystyki
        $stats = [
            'pending' => Report::where('status', 'pending')->count(),
            'reviewing' => Report::where('status', 'reviewing')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
        ];

        return view('moderation.reports', compact('reports', 'stats'));
    }

    /**
     * Oznacz zgłoszenie jako rozwiązane (treść zostanie zdjęta)
     */
    public function resolveReport(Request $request, Report $report): RedirectResponse
    {
        $this->authorize('moderate', Post::class);

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        try {
            DB::transaction(function () use ($report, $request) {
                $report->update([
                    'status' => 'resolved',
                ]);

                // Zdejmij treść z publikacji
                $reportable = $report->reportable;
                if ($reportable) {
                    $reportable->update([
                        'moderation_status' => 'hidden',
                        'moderation_reason' => 'Zgłoszenie rozpatrzone: ' . $request->reason,
                        'moderated_at' => now(),
                        'moderated_by' => auth()->id(),
                    ]);

                    // Log akcji
                    if (method_exists($reportable, 'logModerationAction')) {
                        $reportable->logModerationAction(
                            auth()->user(),
                            'reject',
                            'Zgłoszenie rozpatrzone: ' . $request->reason
                        );
                    }
                }

                // Odrzuć pozostałe zgłoszenia dla tej samej treści
                Report::where('reportable_type', $report->reportable_type)
                    ->where('reportable_id', $report->reportable_id)
                    ->where('id', '!=', $report->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'resolved']);
            });

            return redirect()
                ->route('moderation.reports')
                ->with('success', 'Zgłoszenie rozpatrzone. Treść została ukryta.');
        } catch (\Exception $e) {
            Log::error('Report resolve failed', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['general' => 'Nie udało się rozpatrzyć zgłoszenia.']);
        }
    }

    /**
     * Odrzuć zgłoszenie (treść zostaje)
     */
    public function dismissReport(Request $request, Report $report): RedirectResponse
    {
        $this->authorize('moderate', Post::class);

        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        try {
            DB::transaction(function () use ($report, $request) {
                $report->update([
                    'status' => 'dismissed',
                ]);

                // Log akcji (jeśli treść ma trait Moderatable)
                $reportable = $report->reportable;
                if ($reportable && method_exists($reportable, 'logModerationAction')) {
                    $reportable->logModerationAction(
                        auth()->user(),
                        'approve',
                        'Zgłoszenie odrzucone: ' . $request->reason
                    );
                }
            });

            return redirect()
                ->route('moderation.reports')
                ->with('success', 'Zgłoszenie odrzucone. Treść pozostaje opublikowana.');
        } catch (\Exception $e) {
            Log::error('Report dismiss failed', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['general' => 'Nie udało się odrzucić zgłoszenia.']);
        }
    }

    private function resolvePost(string $type, int $id)
    {
        return match ($type) {
            'anonymous' => AnonymousPost::findOrFail($id),
            'posts' => Post::findOrFail($id),
            'comments' => Comment::findOrFail($id),
            'reports' => Report::findOrFail($id),
            default => abort(404),
        };
    }

    public function history(string $type, int $id)
    {
        $post = $this->resolvePost($type, $id);

        $actions = $post->moderationActions()
            ->with('moderator')
            ->latest()
            ->get();

        return view('moderation.history', compact('post', 'actions', 'type'));
    }
}
