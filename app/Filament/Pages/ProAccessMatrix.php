<?php

namespace App\Filament\Pages;

use App\Models\ProContentType;
use App\Models\ProType;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ProAccessMatrix extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $navigationGroup = 'Espace Pro';

    protected static ?string $title = 'Matrice d\'accès';

    protected static ?string $navigationLabel = 'Matrice d\'accès';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.pro-access-matrix';

    public array $matrix = [];

    public function mount(): void
    {
        $proTypes = ProType::orderBy('sort_order')->get();

        foreach ($proTypes as $proType) {
            $this->matrix[$proType->id] = $proType->contentTypes()->pluck('pro_content_types.id')->toArray();
        }
    }

    public function toggle(int $proTypeId, int $contentTypeId): void
    {
        $proType = ProType::findOrFail($proTypeId);

        if ($proType->contentTypes()->where('pro_content_types.id', $contentTypeId)->exists()) {
            $proType->contentTypes()->detach($contentTypeId);

            if (isset($this->matrix[$proTypeId])) {
                $this->matrix[$proTypeId] = array_values(
                    array_diff($this->matrix[$proTypeId], [$contentTypeId])
                );
            }
        } else {
            $proType->contentTypes()->attach($contentTypeId);

            $this->matrix[$proTypeId][] = $contentTypeId;
        }

        Notification::make()
            ->success()
            ->title('Matrice mise à jour')
            ->duration(1500)
            ->send();
    }

    public function getProTypes()
    {
        return ProType::orderBy('sort_order')->get();
    }

    public function getContentTypes()
    {
        return ProContentType::orderBy('sort_order')->get();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
