<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    /**
     * Wyświetl formularz zmiany hasła
     */
    public function edit()
    {
        return view('auth.change-password');
    }

    /**
     * Zaktualizuj hasło
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            DB::transaction(function () use ($request) {
                $request->user()->update([
                    'password' => Hash::make($request->password),
                ]);
            }, 5);

            // Opcjonalnie: powiadomienie email o zmianie hasła
            // $request->user()->notify(new PasswordChangedNotification());

            return redirect()->route('dashboard')->with('status', 'Hasło zostało zmienione!');
        } catch (\Exception $e) {
            Log::error('Password change failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['general' => 'Nie udało się zmienić hasła.']);
        }
    }
}
