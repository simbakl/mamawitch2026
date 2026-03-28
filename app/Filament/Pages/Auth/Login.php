<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        // Check if user must reset password before allowing login
        $user = User::where('email', $data['email'])->first();

        if ($user?->must_reset_password) {
            Notification::make()
                ->title('Réinitialisation requise')
                ->body('Votre mot de passe doit être réinitialisé. Vérifiez votre boîte email.')
                ->danger()
                ->send();

            return null;
        }

        // Attempt authentication manually for pro users
        if ($user?->hasRole('pro')) {
            if (! auth()->attempt(['email' => $data['email'], 'password' => $data['password']], $data['remember'] ?? false)) {
                throw ValidationException::withMessages([
                    'data.email' => __('filament-panels::pages/auth/login.messages.failed'),
                ]);
            }

            $this->redirect(route('pro.dashboard'));

            return null;
        }

        return parent::authenticate();
    }
}
