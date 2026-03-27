<?php

namespace App\Filament\Resources\ProAccountResource\Pages;

use App\Filament\Resources\ProAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProAccount extends EditRecord
{
    protected static string $resource = ProAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
