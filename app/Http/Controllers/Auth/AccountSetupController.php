<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetRequestMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AccountSetupController extends Controller
{
    /**
     * Show the account setup form (set password + optional Google link).
     */
    public function show(string $token)
    {
        $user = User::where('setup_token', $token)->firstOrFail();

        if (! $user->hasValidSetupToken()) {
            return redirect()->route('login')->with('error', 'Ce lien a expiré. Contactez un administrateur.');
        }

        return view('auth.account-setup', [
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Handle password setup submission.
     */
    public function store(Request $request, string $token)
    {
        $user = User::where('setup_token', $token)->firstOrFail();

        if (! $user->hasValidSetupToken()) {
            return redirect()->route('login')->with('error', 'Ce lien a expiré. Contactez un administrateur.');
        }

        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->update(['password' => bcrypt($request->password)]);
        $user->clearSetupToken();

        Auth::login($user);

        // Redirect pro users to pro dashboard, others to admin
        if ($user->hasRole('pro')) {
            return redirect()->route('pro.dashboard');
        }

        return redirect('/admin');
    }

    /**
     * Show the forgot password form.
     */
    public function forgotPassword()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link via email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $user->generateSetupToken();
            Mail::to($user->email)->send(new PasswordResetRequestMail($user->fresh()));
        }

        return back()->with('success', 'Si cette adresse est associée à un compte, un email de réinitialisation a été envoyé.');
    }
}
