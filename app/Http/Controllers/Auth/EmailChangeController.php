<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmailChangeController extends Controller
{
    /**
     * Show email change form
     */
    public function edit()
    {
        return view('auth.change-email');
    }

    /**
     * Update email
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // 1. Validation
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ]);

        // 2. Update (not verified)
        $user->update([
            'email' => $request->new_email,
            'email_verified_at' => null,
        ]);

        // 3. Verification
        $user->sendEmailVerificationNotification();

        // 4. Redirect with a message
        return redirect()->route('dashboard')->with('status', 
            'Email address changed! Please verify your new email address.'
        );
    }
}