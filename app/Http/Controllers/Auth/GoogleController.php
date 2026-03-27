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
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        // Existing user — link Google if needed and login
        if ($user) {
            if (! $user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
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
            return redirect('/')->with('error', 'Aucun compte associé à cet email.');
        }

        // Create user for the pro
        $user = User::create([
            'name' => $proAccount->full_name,
            'email' => $googleUser->getEmail(),
            'google_id' => $googleUser->getId(),
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
