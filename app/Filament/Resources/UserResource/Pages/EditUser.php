<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Member;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->hasRole('musician') && ! $this->record->member) {
            Member::create([
                'user_id' => $this->record->id,
                'name' => $this->record->name,
                'slug' => \Illuminate\Support\Str::slug($this->record->name),
                'instruments' => 'À définir',
                'sort_order' => Member::max('sort_order') + 1,
            ]);
        }

        if (! $this->record->hasRole('musician') && $this->record->member) {
            $this->record->member->delete();
        }
    }
}
