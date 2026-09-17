<?php

namespace App\Http\Controllers;

use App\Models\AnonymousPost;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AnonymousPostController extends Controller
{
    private const RATE_LIMIT_PER_HOUR = 3;
    private const SPAM_THRESHOLD_REJECT = 80;

    public function create(): View
    {
        return view('posts.anonymous.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:255'],
            'content' => ['required', 'string', 'min:50', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
        ]);

        // ============================================
        // KROK 1: RATE LIMITING (na podstawie IP)
        // ============================================
        $fingerprint = $this->generateFingerprint($request);

        $recentCount = AnonymousPost::where('fingerprint', $fingerprint)
            ->where('created_at', '>=', now()->subHour())
            ->count();

        if ($recentCount >= self::RATE_LIMIT_PER_HOUR) {
            Log::warning('Anonymous post rate limit exceeded', [
                'fingerprint' => substr($fingerprint, 0, 16),
                'count' => $recentCount,
            ]);

            return back()
                ->withInput()
                ->withErrors([
                    'limit' => 'Możesz dodać maksymalnie ' . self::RATE_LIMIT_PER_HOUR . ' posty na godzinę. Spróbuj później.'
                ]);
        }

        // ============================================
        // KROK 2: XSS PROTECTION
        // ============================================
        $content = $request->input('content');

        if ($this->containsXss($validated['content']) || $this->containsXss($validated['title'])) {
            Log::warning('Anonymous post XSS attempt', [
                'fingerprint' => substr($fingerprint, 0, 16),
            ]);
            return back()->withErrors([
                'content' => 'Treść zawiera niedozwolone elementy.'
            ]);
        }

        // ============================================
        // KROK 3: PROFANITY FILTER
        // ============================================
        $content = $this->filterProfanity($validated['content']);
        $title = $validated['title'];

        // ============================================
        // KROK 4: SPAM DETECTION
        // ============================================
        $spamScore = $this->calculateSpamScore($title . ' ' . $content);

        $moderationStatus = $spamScore >= self::SPAM_THRESHOLD_REJECT ? 'rejected' : 'pending';

        // ============================================
        // KROK 5: PERSIST (zapis do bazy)
        // ============================================

        DB::transaction(function () use ($title, $content, $moderationStatus, $spamScore, $fingerprint, $request) {
            $post = AnonymousPost::create([
                'title' => $title,
                'slug' => $this->generateUniqueSlug($title),
                'content' => $content,
                'category' => $validated['category'] ?? 'Ogólne',
                'moderation_status' => $moderationStatus,
                'moderation_reason' => $moderationStatus === 'rejected'
                    ? 'Automatyczna detekcja spamu (score: ' . $spamScore . ')'
                    : null,
                'moderated_at' => $moderationStatus === 'rejected' ? now() : null,
                'fingerprint' => $fingerprint,
                'ip_hash' => hash('sha256', $request->ip() . config('app.key')),
                'user_agent_hash' => hash('sha256', $request->userAgent() . config('app.key')),
            ]);

            // ============================================
            // KROK 6: Log, response
            // ============================================

            Log::info('Anonymous post created', [
                'post_id' => $post->id,
                'spam_score' => $spamScore,
                'moderation_status' => $moderationStatus,
            ]);

            if ($moderationStatus === 'rejected') {
                return redirect()
                    ->route('posts.index')
                    ->with('info', 'Twój post został oznaczony jako spam i odrzucony.');
            }
        });
        return redirect()
            ->route('posts.index')
            ->with('success', 'Twój post został wysłany do moderacji. Pojawi się publicznie po akceptacji.');
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

    private function containsXss(string $content): bool
    {
        $patterns = [
            '/<script\b[^>]*>/i',
            '/<iframe\b[^>]*>/i',
            '/javascript:/i',
            '/on\w+\s*=/i',  // onclick=, onload=, itp.
            '/<embed\b[^>]*>/i',
            '/<object\b[^>]*>/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $content)) {
                return true;
            }
        }

        return false;
    }

    private function filterProfanity(string $content): string
    {
        // Podstawowa lista – możesz rozszerzyć
        $bannedWords = [
            'kurwa',
            'chuj',
            'pizda',
            'jebac',
            'jebać',
            'pierdol',
            'skurwysyn',
            'wypierdalaj',
        ];

        foreach ($bannedWords as $word) {
            $content = preg_replace(
                '/\b' . preg_quote($word, '/') . '\w*\b/iu',
                str_repeat('*', mb_strlen($word)),
                $content
            );
        }

        return $content;
    }

    private function calculateSpamScore(string $content): int
    {
        $score = 0;

        // Linki
        $linkCount = preg_match_all('/https?:\/\//', $content);
        $score += $linkCount * 15;

        // Słowa kluczowe spamu
        $spamKeywords = [
            'kup',
            'promocja',
            'zysk',
            'zarobek',
            'kryptowaluta',
            'bonus',
            'darmowe',
            'kliknij',
            'zarejestruj się',
            'szybki zysk',
            'inwestycja',
            'kredyt',
            'pożyczka',
        ];
        foreach ($spamKeywords as $keyword) {
            if (stripos($content, $keyword) !== false) {
                $score += 10;
            }
        }

        // Powtarzające się znaki (np. "!!!!!!")
        if (preg_match('/(.)\1{5,}/', $content)) {
            $score += 20;
        }

        // Caps lock
        $letters = preg_replace('/[^A-Za-z]/', '', $content);
        if (strlen($letters) > 20) {
            $capsRatio = strlen(preg_replace('/[^A-Z]/', '', $letters)) / strlen($letters);
            if ($capsRatio > 0.5) {
                $score += 15;
            }
        }

        return min($score, 100);
    }

    private function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 1;

        while (AnonymousPost::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
