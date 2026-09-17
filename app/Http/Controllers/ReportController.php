<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\AnonymousPost;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function store(Request $request, string $type, int $id)
    {
        $request->validate([
            'reason' => 'required|in:spam,harassment,hate_speech,sexual_content,violence,misinformation,copyright,other',
            'description' => 'nullable|string|max:1000',
        ]);

        // Rozwiąż obiekt
        $reportable = match ($type) {
            'post' => Post::findOrFail($id),
            'comment' => Comment::findOrFail($id),
            'anonymous' => AnonymousPost::findOrFail($id),
            default => abort(404),
        };

        // Fingerprint dla zapobiegania nadużyciom
        $fingerprint = hash(
            'sha256',
            $request->ip() . '|' . $request->userAgent() . '|' . config('app.key')
        );

        // Sprawdź czy ten fingerprint już nie zgłosił tego obiektu
        $existing = Report::where('reportable_type', get_class($reportable))
            ->where('reportable_id', $reportable->id)
            ->where('reporter_fingerprint', $fingerprint)
            ->exists();

        if ($existing) {
            return back()->with('info', 'Już zgłosiłeś tę treść.');
        }

        try {

            DB::transaction(function () use ($request, $reportable, $fingerprint) {
                Report::create([
                    'reporter_id' => $request->user()?->id,
                    'reportable_type' => get_class($reportable),
                    'reportable_id' => $reportable->id,
                    'reason' => $request->reason,
                    'description' => $request->description,
                    'reporter_fingerprint' => $fingerprint,
                    'status' => 'pending',
                ]);

                // Automatyczne ukrycie po X zgłoszeniach
                $reportCount = $reportable->reports()->count();

                if ($reportCount >= 5 && $reportable->moderation_status === 'approved') {
                    $reportable->update([
                        'moderation_status' => 'pending',
                        'moderation_reason' => "Automatyczne ukrycie po {$reportCount} zgłoszeniach",
                    ]);
                }
            });

            return back()->with('success', 'Zgłoszenie zostało przyjęte. Dziękujemy!');
        } catch (\Exception $e) {
            Log::error('Report failed', [
                'reportable_type' => get_class($reportable),
                'reportable_id' => $reportable->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['general' => 'Nie udało się zapisać zgłoszenia.']);
        }
    }
}
