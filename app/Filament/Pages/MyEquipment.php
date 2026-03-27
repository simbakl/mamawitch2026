<?php

namespace App\Filament\Pages;

use App\Models\Equipment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MyEquipment extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Mon Espace';

    protected static ?string $navigationLabel = 'Mon Matériel';

    protected static ?string $title = 'Mon Matériel';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.my-equipment';

    public ?array $data = [];

    public function mount(): void
    {
        $member = auth()->user()->member;

        if ($member) {
            $this->form->fill([
                'equipment' => $member->equipment->toArray(),
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mon matériel')
                    ->description('Listez tout votre matériel. Ces informations servent à générer la fiche technique du groupe.')
                    ->schema([
                        Forms\Components\Repeater::make('equipment')
                            ->label('')
                            ->schema([
                                Forms\Components\Select::make('category')
                                    ->label('Catégorie')
                                    ->options([
                                        'instrument' => 'Instrument',
                                        'amp' => 'Ampli',
                                        'effect' => 'Effet / Pédale',
                                        'accessory' => 'Accessoire',
                                    ])
                                    ->required()
                                    ->default('instrument'),
                                Forms\Components\TextInput::make('name')
                                    ->label('Nom')
                                    ->required()
                                    ->placeholder('Ex: Gibson Les Paul Standard')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('notes')
                                    ->label('Notes / Détails')
                                    ->placeholder('Ex: accordage Drop D, alimentation 9V...')
                                    ->maxLength(500),
                            ])
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => match ($state['category'] ?? '') {
                                'instrument' => 'Instrument',
                                'amp' => 'Ampli',
                                'effect' => 'Effet',
                                'accessory' => 'Accessoire',
                                default => '',
                            } . ' — ' . ($state['name'] ?? 'Nouveau'))
                            ->collapsible()
                            ->reorderable()
                            ->defaultItems(0)
                            ->addActionLabel('Ajouter un élément'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $member = auth()->user()->member;

        if (! $member) {
            Notification::make()
                ->title('Aucune fiche membre liée')
                ->danger()
                ->send();

            return;
        }

        $member->equipment()->delete();

        $items = $this->form->getState()['equipment'] ?? [];
        foreach ($items as $index => $item) {
            Equipment::create([
                'member_id' => $member->id,
                'name' => $item['name'],
                'category' => $item['category'],
                'notes' => $item['notes'] ?? null,
                'sort_order' => $index,
            ]);
        }

        Notification::make()
            ->title('Matériel sauvegardé')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('musician') ?? false;
    }
}
