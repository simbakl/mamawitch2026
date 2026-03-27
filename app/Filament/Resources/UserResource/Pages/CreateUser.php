<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\AccountSetupMail;
use App\Models\Member;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        // Generate setup token and send invitation email
        $this->record->generateSetupToken();
        Mail::to($this->record->email)->send(new AccountSetupMail($this->record->fresh()));

        // Auto-create Member if musician role assigned
        if ($this->record->hasRole('musician') && ! $this->record->member) {
            Member::create([
                'user_id' => $this->record->id,
                'name' => $this->record->name,
                'slug' => \Illuminate\Support\Str::slug($this->record->name),
                'instruments' => 'À définir',
                'sort_order' => Member::max('sort_order') + 1,
            ]);
        }

        Notification::make()
            ->title('Utilisateur créé')
            ->body('Un email de configuration a été envoyé à ' . $this->record->email)
            ->success()
            ->send();
    }
}
