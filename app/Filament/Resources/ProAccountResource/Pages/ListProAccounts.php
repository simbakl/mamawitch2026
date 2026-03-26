<?php

namespace App\Filament\Resources\ProAccountResource\Pages;

use App\Filament\Resources\ProAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProAccounts extends ListRecords
{
    protected static string $resource = ProAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Inviter un pro'),
        ];
    }
}
