<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Wyświetl formularz "Zapomniałem hasła"
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Wyślij link resetu hasła na email
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Wyślij link resetu
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Przekieruj z komunikatem
        if ($status === Password::RESET_LINK_SENT) {
            return back()->with(['status' => __($status)]);
        }

        return back()->withErrors(['email' => __($status)]);
    }
}