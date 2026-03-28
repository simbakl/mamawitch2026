<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class SiteSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Paramètres du site';

    protected static ?string $title = 'Paramètres du site';

    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.site-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'hero_image' => SiteSetting::get('hero_image'),
            'hero_logo_enabled' => SiteSetting::get('hero_logo_enabled', '1'),
            'hero_logo_image' => SiteSetting::get('hero_logo_image'),
            'hero_title' => SiteSetting::get('hero_title', 'Mama Witch'),
            'hero_subtitle' => SiteSetting::get('hero_subtitle'),
            'hero_cta_text' => SiteSetting::get('hero_cta_text'),
            'hero_cta_url' => SiteSetting::get('hero_cta_url'),
            'social_facebook' => SiteSetting::get('social_facebook', 'https://facebook.com/littlemamawitch'),
            'social_instagram' => SiteSetting::get('social_instagram'),
            'social_youtube' => SiteSetting::get('social_youtube'),
            'social_twitter' => SiteSetting::get('social_twitter', 'https://x.com/mamawitchoff'),
            'contact_email' => SiteSetting::get('contact_email', 'contact@mamawitch.fr'),
            'meta_description' => SiteSetting::get('meta_description', 'Mama Witch - Groupe de Hard Rock - Paris'),
            'google_analytics_id' => SiteSetting::get('google_analytics_id'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Paramètres')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Hero (Accueil)')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\FileUpload::make('hero_image')
                                    ->label('Image / Vidéo de fond')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/*', 'video/mp4'])
                                    ->maxSize(10240),
                                Forms\Components\Toggle::make('hero_logo_enabled')
                                    ->label('Afficher le logo dans le hero')
                                    ->default(true)
                                    ->live()
                                    ->inline(false),
                                Forms\Components\FileUpload::make('hero_logo_image')
                                    ->label('Logo personnalisé (optionnel)')
                                    ->helperText('Laissez vide pour utiliser le logo par défaut.')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/*'])
                                    ->maxSize(5120)
                                    ->visible(fn (Forms\Get $get) => (bool) $get('hero_logo_enabled')),
                                Forms\Components\TextInput::make('hero_title')
                                    ->label('Titre principal')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('hero_subtitle')
                                    ->label('Sous-titre / Accroche')
                                    ->maxLength(255)
                                    ->placeholder('Ex: Nouveau EP disponible'),
                                Forms\Components\TextInput::make('hero_cta_text')
                                    ->label('Texte du bouton')
                                    ->maxLength(100)
                                    ->placeholder('Ex: Écouter maintenant'),
                                Forms\Components\TextInput::make('hero_cta_url')
                                    ->label('Lien du bouton')
                                    ->url()
                                    ->placeholder('https://...'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Réseaux sociaux')
                            ->icon('heroicon-o-share')
                            ->schema([
                                Forms\Components\TextInput::make('social_facebook')->label('Facebook')->url(),
                                Forms\Components\TextInput::make('social_instagram')->label('Instagram')->url(),
                                Forms\Components\TextInput::make('social_youtube')->label('YouTube')->url(),
                                Forms\Components\TextInput::make('social_twitter')->label('X (Twitter)')->url(),
                            ])->columns(2),

                        Forms\Components\Tabs\Tab::make('Général')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\TextInput::make('contact_email')
                                    ->label('Email de contact')
                                    ->email(),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Description SEO')
                                    ->rows(2)
                                    ->maxLength(160)
                                    ->helperText('Affiché dans les résultats Google (max 160 caractères)'),
                                Forms\Components\TextInput::make('google_analytics_id')
                                    ->label('Google Analytics ID')
                                    ->placeholder('G-XXXXXXXXXX')
                                    ->helperText('ID de mesure Google Analytics 4. Laissez vide pour désactiver le tracking.'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            SiteSetting::set($key, $value);
        }

        Notification::make()
            ->title('Paramètres sauvegardés')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
