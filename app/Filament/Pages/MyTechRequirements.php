<?php

namespace App\Filament\Pages;

use App\Models\TechRequirement;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MyTechRequirements extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-speaker-wave';

    protected static ?string $navigationGroup = 'Mon Espace';

    protected static ?string $navigationLabel = 'Mes Besoins Techniques';

    protected static ?string $title = 'Mes Besoins Techniques';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.my-tech-requirements';

    public ?array $data = [];

    public function mount(): void
    {
        $member = auth()->user()->member;

        if ($member?->techRequirement) {
            $this->form->fill($member->techRequirement->toArray());
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Ce que la salle doit fournir')
                    ->description('Renseignez vos besoins personnels. Ces informations seront agrégées dans la fiche technique du groupe.')
                    ->schema([
                        Forms\Components\Textarea::make('monitors')
                            ->label('Retours / Monitors')
                            ->rows(2)
                            ->placeholder('Ex: 1 retour bain de pied, mix séparé'),
                        Forms\Components\Textarea::make('microphones')
                            ->label('Micros / DI')
                            ->rows(2)
                            ->placeholder('Ex: 1 SM57 pour ampli guitare, 1 DI pour acoustique'),
                        Forms\Components\Textarea::make('power')
                            ->label('Électricité')
                            ->rows(2)
                            ->placeholder('Ex: 2 prises secteur côté jardin'),
                        Forms\Components\Textarea::make('monitoring')
                            ->label('Monitoring')
                            ->rows(2)
                            ->placeholder('Ex: 1 mix retour séparé avec voix + guitare'),
                        Forms\Components\Textarea::make('other')
                            ->label('Divers')
                            ->rows(2)
                            ->placeholder('Ex: tabouret, tapis, riser...')
                            ->columnSpanFull(),
                    ])->columns(2),
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

        TechRequirement::updateOrCreate(
            ['member_id' => $member->id],
            $this->form->getState()
        );

        Notification::make()
            ->title('Besoins techniques sauvegardés')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('musician') ?? false;
    }
}
