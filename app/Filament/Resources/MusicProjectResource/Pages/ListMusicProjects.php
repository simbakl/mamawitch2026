<?php

namespace App\Filament\Resources\MusicProjectResource\Pages;

use App\Filament\Resources\MusicProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMusicProjects extends ListRecords
{
    protected static string $resource = MusicProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
