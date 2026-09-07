<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController; 
use App\Http\Controllers\Auth\NewPasswordController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest routes (not logged in)
Route::middleware('guest')->group(function () {
    
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    
     Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store']);

    // Formularz "Zapomniałem hasła"
    Route::get('forgot-password', function () {
        return view('auth.forgot-password');
    })->name('password.request');

    // Wysyłanie linku resetu
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    // Formularz resetu hasła (z tokenem w URL)
    Route::get('reset-password/{token}', function (string $token) {
        return view('auth.reset-password', ['token' => $token]);
    })->name('password.reset');

    // Zapis nowego hasła
    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.update');
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

    // ✅ Change Password routes (dodaj to!)
    Route::get('change-password', [App\Http\Controllers\Auth\PasswordChangeController::class, 'edit'])
        ->name('password.change');
    Route::post('change-password', [App\Http\Controllers\Auth\PasswordChangeController::class, 'update'])
        ->name('password.update');
        
    // Protected routes (require verified email)
    Route::middleware('verified')->group(function () {
        Route::get('/dashboard', function () {
            return view('dashboard');
        })->name('dashboard');
        
        // Add other protected routes here
    });
});