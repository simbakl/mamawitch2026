<?php

namespace App\Filament\Resources\GalleryResource\Pages;

use App\Filament\Resources\GalleryResource;
use App\Models\GalleryPhoto;
use Filament\Resources\Pages\CreateRecord;

class CreateGallery extends CreateRecord
{
    protected static string $resource = GalleryResource::class;

    protected function afterCreate(): void
    {
        $bulkPhotos = $this->data['bulk_photos'] ?? [];

        if (! empty($bulkPhotos)) {
            $order = 0;
            foreach ($bulkPhotos as $photo) {
                $order++;
                GalleryPhoto::create([
                    'gallery_id' => $this->record->id,
                    'image' => $photo,
                    'caption' => 'Photo ' . $order,
                    'sort_order' => $order,
                ]);
            }
        }
    }
}
