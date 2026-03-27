<?php

namespace App\Filament\Resources\ProAccountResource\Pages;

use App\Filament\Resources\ProAccountResource;
use Filament\Resources\Pages\CreateRecord;
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
}
