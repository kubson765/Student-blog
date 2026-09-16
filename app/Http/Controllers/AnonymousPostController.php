<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AnonymousPost;
use App\Models\Tag;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnonymousPostController extends Controller
{
    public function store(Request $request)
    {
        // ============================================
        // KROK 1: RATE LIMITING (na podstawie IP)
        // ============================================
        $fingerprint = $this->generateFingerprint($request);

        $recentCount = AnonymousPost::where('fingerprint', $fingerprint)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentCount >= 3) {
            return back()->withErrors([
                'limit' => 'Możesz dodać maksymalnie 3 posty na godzinę. Spróbuj później.'
            ]);
        }

        // ============================================
        // KROK 2: XSS PROTECTION
        // ============================================
        $content = $request->input('content');

        if ($this->containsXss($content)) {
            return back()->withErrors([
                'content' => 'Treść zawiera niedozwolone elementy.'
            ]);
        }

        // ============================================
        // KROK 3: PROFANITY FILTER (wulgaryzmy)
        // ============================================
        $content = $this->filterProfanity($content);

        // ============================================
        // KROK 4: SPAM DETECTION (AI/Reguły)
        // ============================================
        $spamScore = $this->calculateSpamScore($content);

        if ($spamScore > 80) {
            // Odrzuć od razu – to spam
            AnonymousPost::create([
                'title' => $request->title,
                'content' => $content,
                'fingerprint' => $fingerprint,
                'status' => 'spam',
                'moderation_reason' => 'Automatyczna detekcja spamu (score: ' . $spamScore . ')',
            ]);

            return redirect()->route('posts.index')
                ->with('info', 'Twój post został oznaczony do moderacji.');
        }

        // ============================================
        // KROK 5: PERSIST (zapis do bazy)
        // ============================================
        $post = AnonymousPost::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'content' => $content,
            'category' => $request->category ?? 'Ogólne',
            'fingerprint' => $fingerprint,
            'ip_hash' => hash('sha256', $request->ip() . config('app.key')),
            'user_agent_hash' => hash('sha256', $request->userAgent() . config('app.key')),
            'status' => $spamScore > 50 ? 'pending' : 'approved', // auto-approve dla dobrej treści
        ]);

        // ============================================
        // KROK 6: POWIADOM MODERATORA (jeśli pending)
        // ============================================
        if ($post->status === 'pending') {
            // Wyślij email do admina lub zapisz w kolejce
            Log::channel('moderation')->info('New anonymous post for review', [
                'post_id' => $post->id,
                'spam_score' => $spamScore,
            ]);
        }

        // ============================================
        // KROK 7: RESPONSE
        // ============================================
        return redirect()->route('posts.index')
            ->with('success', 'Twój post został dodany!');
    }

    private function generateFingerprint(Request $request): string
    {
        // Kombinacja IP + User-Agent + sekret
        // Dzięki temu ten sam użytkownik ma ten sam fingerprint,
        // ale nie da się go odwrócić do IP
        return hash(
            'sha256',
            $request->ip() . '|' .
                $request->userAgent() . '|' .
                config('app.key') // sekret, żeby nikt z zewnątrz nie mógł odtworzyć
        );
    }
}
