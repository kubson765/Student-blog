<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AccountDeletionController extends Controller
{
    /**
     * Show account termination form
     */
    public function edit()
    {
        return view('auth.delete-account');
    }

    /**
     * Delete account
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // 1. Password validation
        $request->validate([
            'password' => ['required', 'current_password'],
            'confirmation' => ['required', 'accepted'],
        ]);

        $userId = $user->id;
        $userEmail = $user->email;

        try {
            DB::transaction(function () use ($user) {
                $this->anonymizeUserData($user);
                $user->delete();
            });

            Log::info('Account deleted', [
                'user_id' => $userId,
                'email' => $userEmail,
            ]);
        } catch (\Exception $e) {
            Log::error('Account deletion failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'general' => 'Could not remove your account. Try again or contact helpdesk.'
            ]);
        }

        // 4. Logout
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // 5. Redirect and confirmation message
        return redirect('/')->with('status', 'Your account has been deleted successfully.');
    }

    // Clearing user activity
    protected function anonymizeUserData(User $user): void
    {
        $user->posts()->update([
            'user_id' => null,
            // 'title' => DB::raw("CONCAT('Deleted Post ', id)"),
            // 'content' => 'This content has been removed by the user.',
        ]);

        $user->comments()->update([
            'user_id' => null,
            // 'content' => 'This comment has been removed by the user.',
        ]);

        $user->interactions()->delete();
    }
}
