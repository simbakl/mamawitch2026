<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\Member;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
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
    }
}
