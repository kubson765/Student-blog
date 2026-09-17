<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnonymousPost;
use App\Models\Comment;
use App\Models\Interaction;
use App\Models\ModerationAction;
use App\Models\Post;
use App\Models\Report;
use App\Models\Tag;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ===== Stats =====
        $stats = [
            'users' => [
                'total' => User::count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'moderators' => User::whereIn('role', ['moderator', 'admin'])->count(),
                'admins' => User::where('role', 'admin')->count(),
                'banned' => User::whereNotNull('banned_at')->count(),
                'new_last_7_days' => User::where('created_at', '>=', now()->subDays(7))->count(),
            ],
            'posts' => [
                'total' => Post::count(),
                'published' => Post::where('status', 'published')
                    ->where('moderation_status', 'approved')->count(),
                'drafts' => Post::where('status', 'draft')->count(),
                'pending' => Post::where('moderation_status', 'pending')->count(),
                'rejected' => Post::where('moderation_status', 'rejected')->count(),
            ],
            'anonymous_posts' => [
                'total' => AnonymousPost::count(),
                'pending' => AnonymousPost::where('moderation_status', 'pending')->count(),
                'approved' => AnonymousPost::where('moderation_status', 'approved')->count(),
                'rejected' => AnonymousPost::where('moderation_status', 'rejected')->count(),
            ],
            'comments' => [
                'total' => Comment::count(),
                'pending' => Comment::where('moderation_status', 'pending')->count(),
                'approved' => Comment::where('moderation_status', 'approved')->count(),
                'rejected' => Comment::where('moderation_status', 'rejected')->count(),
            ],
            'reports' => [
                'total' => Report::count(),
                'pending' => Report::where('status', 'pending')->count(),
                'resolved' => Report::where('status', 'resolved')->count(),
                'dismissed' => Report::where('status', 'dismissed')->count(),
            ],
            'interactions' => [
                'total' => Interaction::count(),
            ],
            'tags' => [
                'total' => Tag::count(),
            ],
        ];

        // ===== Moderation queue =====
        $moderationQueue = [
            'anonymous' => $stats['anonymous_posts']['pending'],
            'posts' => $stats['posts']['pending'],
            'comments' => $stats['comments']['pending'],
            'reports' => $stats['reports']['pending'],
        ];

        // ===== Latest reports =====
        $recentReports = Report::with(['reporter', 'reportable'])
            ->where('status', 'pending')
            ->latest()
            ->take(10)
            ->get();

        // ===== Latest registered users =====
        $recentUsers = User::latest()
            ->take(10)
            ->get();

        // ===== Latest moderation =====
        $recentModerationActions = ModerationAction::with(['moderator', 'moderatable'])
            ->latest()
            ->take(10)
            ->get();

        // ===== Top 5 tags =====
        $topTags = Tag::withCount('posts')
            ->orderByDesc('posts_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'moderationQueue',
            'recentReports',
            'recentUsers',
            'recentModerationActions',
            'topTags',
        ));
    }
}
