<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ModerationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CommentController;


// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');


// Guest routes (Account-related)
Route::middleware('guest')->group(function () {

    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    // Forgot password form
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    // Send reset link
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Password reset form (with token in URL)
    Route::get('reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    // Store new password
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

// Authenticated routes (logged in)
Route::middleware('auth')->group(function () {

    // Email verification routes
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('status', 'verification-link-sent');
    })->middleware('throttle:6,1')->name('verification.send');

    Route::post('logout', [LogoutController::class, 'logout'])->name('logout');

    // Delete Account
    Route::get('delete-account', [App\Http\Controllers\Auth\AccountDeletionController::class, 'edit'])
        ->name('account.delete');
    Route::delete('delete-account', [App\Http\Controllers\Auth\AccountDeletionController::class, 'destroy'])
        ->name('account.destroy');

    // Protected routes (require verified email)
    Route::middleware('verified')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');

        // Change Password
        Route::get('change-password', [App\Http\Controllers\Auth\PasswordChangeController::class, 'edit'])
            ->name('password.change');
        Route::post('change-password', [App\Http\Controllers\Auth\PasswordChangeController::class, 'update'])
            ->name('password.update');

        // Change email
        Route::get('change-email', [App\Http\Controllers\Auth\EmailChangeController::class, 'edit'])
            ->name('email.change');
        Route::post('change-email', [App\Http\Controllers\Auth\EmailChangeController::class, 'update'])
            ->name('email.update');
    });
});


// ============================================
// POSTS ROUTES (in order (.show last))
// ============================================

// Publiczne
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');

// Chronione (PRZED /posts/{post}!)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/posts/create', [PostController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::get('/posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
    Route::put('/posts/{post}', [PostController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
});

// Wildcard NA KOŃCU
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');

// ============================================
// COMMENTS ROUTES
// ============================================

// Public
Route::get('/posts/{post}/comments', [CommentController::class, 'index'])->name('comments.index');

// logged in
Route::middleware(['auth', 'verified'])->group(function () {
    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::get('/comments/{comment}/edit', [CommentController::class, 'edit'])->name('comments.edit');
    Route::put('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});


// Moderation routes
Route::middleware(['auth', 'verified'])->prefix('moderation')->group(function () {
    Route::get('/', [ModerationController::class, 'index'])->name('moderation.index');
    Route::get('/{type}/{id}', [ModerationController::class, 'show'])->name('moderation.show');

    Route::post('/{type}/{id}/approve', [ModerationController::class, 'approve'])->name('moderation.approve');
    Route::post('/{type}/{id}/reject', [ModerationController::class, 'reject'])->name('moderation.reject');
    Route::post('/{type}/{id}/spam', [ModerationController::class, 'spam'])->name('moderation.spam');
    Route::post('/{type}/{id}/escalate', [ModerationController::class, 'escalate'])->name('moderation.escalate');
    Route::get('/{type}/{id}/history', [ModerationController::class, 'history'])->name('moderation.history');
});

// Admin routes
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/moderation/audit', [\App\Http\Controllers\Admin\ModerationAuditController::class, 'index'])
        ->name('admin.moderation.audit');
});

// Report routes
Route::post('/report/{type}/{id}', [ReportController::class, 'store'])
    ->name('report.store')
    ->middleware('throttle:10,60'); // 10 reports per hour per IP
