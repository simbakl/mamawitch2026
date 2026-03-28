<?php

namespace App\Filament\Pages;

use App\Models\Member;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MyProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Mon Espace';

    protected static ?string $navigationLabel = 'Ma Fiche';

    protected static ?string $title = 'Ma Fiche Membre';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.my-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $member = auth()->user()->member;

        if ($member) {
            $this->form->fill($member->toArray());
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mon profil public')
                    ->description('Ces informations sont visibles sur le site public')
                    ->schema([
                        Forms\Components\FileUpload::make('photo')
                            ->label('Ma photo')
                            ->image()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('3:4')
                            ->imageResizeTargetWidth('600')
                            ->imageResizeTargetHeight('800')
                            ->directory('members')
                            ->visibility('public')
                            ->maxSize(5120),
                        Forms\Components\TextInput::make('instruments')
                            ->label('Instrument(s)')
                            ->required()
                            ->placeholder('Ex: Guitare, Chant'),
                        Forms\Components\Textarea::make('bio')
                            ->label('Bio courte')
                            ->rows(4)
                            ->maxLength(1000),
                    ]),

                Forms\Components\Section::make('Mes réseaux sociaux')
                    ->description('Liens vers vos profils personnels (optionnel)')
                    ->schema([
                        Forms\Components\TextInput::make('instagram')
                            ->label('Instagram')
                            ->url()
                            ->placeholder('https://instagram.com/...'),
                        Forms\Components\TextInput::make('facebook')
                            ->label('Facebook')
                            ->url()
                            ->placeholder('https://facebook.com/...'),
                        Forms\Components\TextInput::make('twitter')
                            ->label('X (Twitter)')
                            ->url()
                            ->placeholder('https://x.com/...'),
                        Forms\Components\TextInput::make('youtube')
                            ->label('YouTube')
                            ->url()
                            ->placeholder('https://youtube.com/...'),
                        Forms\Components\TextInput::make('website')
                            ->label('Site web')
                            ->url()
                            ->placeholder('https://...'),
                    ])->columns(2),

            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $member = auth()->user()->member;

        if (! $member) {
            Notification::make()
                ->title('Aucune fiche membre liée à votre compte')
                ->body('Contactez l\'administrateur pour lier votre compte à une fiche membre.')
                ->danger()
                ->send();

            return;
        }

        $member->update($this->form->getState());

        Notification::make()
            ->title('Fiche mise à jour')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('musician') ?? false;
    }
}
