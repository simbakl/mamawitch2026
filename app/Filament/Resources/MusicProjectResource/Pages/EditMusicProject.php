<?php

namespace App\Filament\Resources\MusicProjectResource\Pages;

use App\Filament\Resources\MusicProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditMusicProject extends EditRecord
{
    protected static string $resource = MusicProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Add track IDs to form data so the player can find them
        if ($this->record) {
            $data['tracks'] = $this->record->tracks()->orderBy('sort_order')->get()->map(function ($track) {
                return array_merge($track->toArray(), [
                    'id' => $track->id,
                ]);
            })->toArray();
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Clean up orphaned audio files if tracks were removed
        $disk = Storage::disk('pro-audio');
        $existingTrackIds = $this->record->tracks()->pluck('id')->toArray();

        // The repeater handles deletion of removed tracks automatically
        // but we need to clean up the physical files for deleted tracks
    }
}
