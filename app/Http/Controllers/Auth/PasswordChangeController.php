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
        // 1. Walidacja
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        // 2. Aktualizacja hasła
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        // 3. Przekierowanie z komunikatem
        return redirect()->route('dashboard')->with('status', 'Password changed successfully!');
    }
}