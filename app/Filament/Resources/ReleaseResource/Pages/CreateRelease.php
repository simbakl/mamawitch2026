<?php

namespace App\Filament\Resources\ReleaseResource\Pages;

use App\Filament\Resources\ReleaseResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRelease extends CreateRecord
{
    protected static string $resource = ReleaseResource::class;

    protected function afterCreate(): void
    {
        $this->record->tracks()->each(function ($track, $index) {
            $track->update(['track_number' => $index + 1]);
        });
    }
}
