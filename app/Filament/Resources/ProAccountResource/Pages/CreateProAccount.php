<?php

namespace App\Filament\Resources\ProAccountResource\Pages;

use App\Filament\Resources\ProAccountResource;
use App\Mail\ProInvitationMail;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class CreateProAccount extends CreateRecord
{
    protected static string $resource = ProAccountResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'invited';
        $data['invitation_token'] = Str::random(64);
        $data['invitation_sent_at'] = now();

        return $data;
    }

    protected function afterCreate(): void
    {
        Mail::to($this->record->email)->send(new ProInvitationMail($this->record));

        Notification::make()
            ->title('Invitation envoyée')
            ->body('Un email d\'invitation a été envoyé à ' . $this->record->email)
            ->success()
            ->send();
    }
}
