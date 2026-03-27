<?php

namespace App\Filament\Resources\ProTypeResource\Pages;

use App\Filament\Resources\ProTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageProTypes extends ManageRecords
{
    protected static string $resource = ProTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
