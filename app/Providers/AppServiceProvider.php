<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(\App\Models\Post::class, \App\Policies\PostPolicy::class);
        Gate::policy(\App\Models\Comment::class, \App\Policies\CommentPolicy::class);
        Gate::policy(\App\Models\AnonymousPost::class, \App\Policies\AnonymousPostPolicy::class);
        Gate::policy(\App\Models\ModerationAction::class, \App\Policies\ModerationActionPolicy::class);

        RateLimiter::for('moderation', function (Request $request) {
            return Limit::perMinute(30)->by($request->user()->id);
        });

        RateLimiter::for('reports', function (Request $request) {
            return Limit::perHour(10)->by($request->ip());
        });

        RateLimiter::for('anonymous-posts', function (Request $request) {
            return Limit::perHour(10)->by($request->ip());
        });

        Paginator::useBootstrapFive();
    }
}
