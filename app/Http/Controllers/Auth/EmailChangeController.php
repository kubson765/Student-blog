<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_email' => ['required', 'email', 'unique:users,email,' . $user->id],
        ]);

        try {
            DB::transaction(function () use ($user, $request) {
                $user->update([
                    'email' => $request->new_email,
                    'email_verified_at' => null,
                ]);
            });

            // Sned mail after successful transaction
            $user->sendEmailVerificationNotification();

            return redirect()->route('dashboard')->with(
                'status',
                'Email został zmieniony! Sprawdź nową skrzynkę, aby zweryfikować adres.'
            );
        } catch (\Exception $e) {
            Log::error('Email change failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'Nie udało się zmienić emaila.']);
        }
    }
}
