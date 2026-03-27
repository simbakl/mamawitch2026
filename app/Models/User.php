<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
        'setup_token',
        'setup_token_expires_at',
        'must_reset_password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'setup_token_expires_at' => 'datetime',
            'must_reset_password' => 'boolean',
        ];
    }

    /**
     * Generate a setup token for account activation or password reset.
     */
    public function generateSetupToken(): string
    {
        $token = Str::random(64);
        $this->update([
            'setup_token' => $token,
            'setup_token_expires_at' => now()->addHours(48),
        ]);

        return $token;
    }

    /**
     * Clear the setup token after use.
     */
    public function clearSetupToken(): void
    {
        $this->update([
            'setup_token' => null,
            'setup_token_expires_at' => null,
            'must_reset_password' => false,
        ]);
    }

    /**
     * Check if the setup token is valid.
     */
    public function hasValidSetupToken(): bool
    {
        return $this->setup_token
            && $this->setup_token_expires_at
            && $this->setup_token_expires_at->isFuture();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->hasAnyRole(['admin', 'editor', 'musician']);
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isEditor(): bool
    {
        return $this->hasRole('editor');
    }

    public function isMusician(): bool
    {
        return $this->hasRole('musician');
    }

    public function isPro(): bool
    {
        return $this->hasRole('pro');
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class);
    }

    public function proAccount(): HasOne
    {
        return $this->hasOne(ProAccount::class);
    }
}
