<?php

namespace App\Filament\Resources\GalleryResource\Pages;

use App\Filament\Resources\GalleryResource;
use App\Models\GalleryPhoto;
use Filament\Actions;
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
        $bulkPhotos = $this->data['bulk_photos'] ?? [];

        if (! empty($bulkPhotos)) {
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
        }
    }
}
