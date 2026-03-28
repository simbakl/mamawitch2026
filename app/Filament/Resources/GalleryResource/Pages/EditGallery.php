<?php

namespace App\Filament\Resources\GalleryResource\Pages;

use App\Filament\Resources\GalleryResource;
use App\Models\GalleryPhoto;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditGallery extends EditRecord
{
    protected static string $resource = GalleryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $count = $this->createBulkPhotos();
        if ($count > 0) {
            Notification::make()->success()->title($count . ' photo(s) ajoutée(s)')->send();
        }
    }

    protected function createBulkPhotos(): int
    {
        $bulkPhotos = $this->form->getRawState()['bulk_photos'] ?? [];

        if (empty($bulkPhotos)) {
            return 0;
        }

        $maxOrder = $this->record->photos()->max('sort_order') ?? 0;

        foreach ($bulkPhotos as $photo) {
            $maxOrder++;
            GalleryPhoto::create([
                'gallery_id' => $this->record->id,
                'image' => $photo,
                'caption' => 'Photo ' . $maxOrder,
                'sort_order' => $maxOrder,
            ]);
        }

        return count($bulkPhotos);
    }

    protected function getRedirectUrl(): string
    {
        return GalleryResource::getUrl('edit', ['record' => $this->record]);
    }
}
