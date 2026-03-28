<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ProAccount;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        // Store context for after Google redirect
        if (Auth::check()) {
            session(['google_link_user_id' => Auth::id()]);
        }

        // Remember where the user came from for error redirects
        $referer = url()->previous();
        session(['google_redirect_back' => $referer]);

        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        $redirectBack = session()->pull('google_redirect_back', '/admin/login');
        $linkUserId = session()->pull('google_link_user_id');

        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return redirect($redirectBack)->with('error', 'Erreur lors de la connexion avec Google. Veuillez réessayer.');
        }

        if ($linkUserId) {
            return $this->linkGoogle($googleUser, $linkUserId, $redirectBack);
        }

        return $this->loginOrRegister($googleUser, $redirectBack);
    }

    private function linkGoogle($googleUser, int $userId, string $redirectBack)
    {
        $currentUser = User::findOrFail($userId);

        // Check if this Google account is already linked to another user
        $existingUser = User::where('google_id', $googleUser->getId())->first();
        if ($existingUser && $existingUser->id !== $currentUser->id) {
            return redirect($redirectBack)->with('error', 'Ce compte Google est déjà lié à un autre utilisateur.');
        }

        $currentUser->update([
            'google_id' => $googleUser->getId(),
            'google_email' => $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
        ]);

        Auth::login($currentUser, true);

        if ($currentUser->hasRole('pro')) {
            return redirect()->route('pro.dashboard')->with('success', 'Compte Google lié avec succès.');
        }

        return redirect('/admin/my-account')->with('success', 'Compte Google lié avec succès.');
    }

    private function loginOrRegister($googleUser, string $redirectBack)
    {
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        // Existing user — link Google if needed and login
        if ($user) {
            if (! $user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_email' => $googleUser->getEmail(),
                    'avatar' => $googleUser->getAvatar(),
                ]);
            }

            // Clear any pending setup token (user chose Google instead)
            if ($user->setup_token) {
                $user->clearSetupToken();
            }

            Auth::login($user, true);

            if ($user->hasRole('pro')) {
                return redirect()->route('pro.dashboard');
            }

            return redirect('/admin');
        }

        // No existing user — check if there's an approved/invited ProAccount for this email
        $proAccount = ProAccount::where('email', $googleUser->getEmail())
            ->whereIn('status', ['approved', 'invited'])
            ->first();

        if (! $proAccount) {
            return redirect($redirectBack)->with('error', 'Aucun compte associé à cet email. Veuillez d\'abord créer un compte avec votre mot de passe.');
        }

        // Create user for the pro
        $user = User::create([
            'name' => $proAccount->full_name,
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
            'google_email' => $googleUser->getEmail(),
            'avatar' => $googleUser->getAvatar(),
        ]);

        $user->assignRole('pro');

        // Link and activate the pro account
        $proAccount->update([
            'user_id' => $user->id,
            'status' => 'approved',
            'approved_at' => $proAccount->approved_at ?? now(),
        ]);

        Auth::login($user, true);

        return redirect()->route('pro.dashboard');
    }
}
