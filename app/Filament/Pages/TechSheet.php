<?php

namespace App\Filament\Pages;

use App\Models\GlobalTechRequirement;
use App\Models\Member;
use App\Models\StagePlan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class TechSheet extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $navigationGroup = 'Mon Espace';

    protected static ?string $navigationLabel = 'Fiche Technique';

    protected static ?string $title = 'Fiche Technique du Groupe';

    protected static ?int $navigationSort = 5;

    protected static string $view = 'filament.pages.tech-sheet';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'setup_time' => GlobalTechRequirement::get('setup_time'),
            'soundcheck_time' => GlobalTechRequirement::get('soundcheck_time'),
            'teardown_time' => GlobalTechRequirement::get('teardown_time'),
            'global_notes' => GlobalTechRequirement::get('global_notes'),
            'global_monitors' => GlobalTechRequirement::get('global_monitors'),
            'global_other' => GlobalTechRequirement::get('global_other'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Besoins globaux du groupe')
                    ->description('Informations complémentaires non rattachées à un musicien en particulier')
                    ->schema([
                        Forms\Components\TextInput::make('setup_time')
                            ->label('Temps de montage')
                            ->placeholder('Ex: 30 minutes'),
                        Forms\Components\TextInput::make('soundcheck_time')
                            ->label('Temps de balance')
                            ->placeholder('Ex: 30 minutes'),
                        Forms\Components\TextInput::make('teardown_time')
                            ->label('Temps de démontage')
                            ->placeholder('Ex: 20 minutes'),
                        Forms\Components\Textarea::make('global_monitors')
                            ->label('Besoins sono / retours globaux')
                            ->rows(3)
                            ->placeholder('Ex: Minimum 4 retours bain de pied, side-fills...'),
                        Forms\Components\Textarea::make('global_other')
                            ->label('Besoins divers')
                            ->rows(3)
                            ->placeholder('Ex: Loges, serviettes, eau, accès parking...'),
                        Forms\Components\Textarea::make('global_notes')
                            ->label('Notes complémentaires')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            GlobalTechRequirement::set($key, $value);
        }

        Notification::make()
            ->title('Besoins globaux sauvegardés')
            ->success()
            ->send();
    }

    public function getMembers()
    {
        return Member::with(['equipment', 'techRequirement'])->orderBy('sort_order')->get();
    }

    public function getStagePlan()
    {
        return StagePlan::first();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('musician') ?? false;
    }
}
