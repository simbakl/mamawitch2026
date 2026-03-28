<?php

namespace App\Filament\Pages;

use App\Models\ProContentPage;
use App\Models\ProContentType;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ProContent extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Espace Pro';

    protected static ?string $title = 'Contenus Pro';

    protected static ?string $navigationLabel = 'Contenus Pro';

    protected static ?int $navigationSort = 4;

    protected static string $view = 'filament.pages.pro-content';

    public ?array $data = [];

    public function mount(): void
    {
        $pages = ProContentPage::with('contentType')->get()->keyBy(fn ($p) => $p->contentType->slug);

        $this->form->fill([
            'hospitality_rider' => $pages['hospitality-rider']?->body ?? '',
            'bio_longue_presse' => $pages['bio-longue-presse']?->body ?? '',
            'conditions_booking' => $pages['conditions-booking']?->body ?? '',
            'contacts' => $pages['contact-booking-direct']?->data ?? [],
            'revue_de_presse' => $pages['revue-de-presse']?->data ?? [],
            'photos_hd' => $pages['photos-hd']?->files ?? [],
            'logos_vectoriels' => $pages['logos-vectoriels']?->files ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Contenus')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Rider')
                            ->icon('heroicon-o-home')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content('Besoins en loges : boissons, repas, serviettes, etc.'),
                                Forms\Components\RichEditor::make('hospitality_rider')
                                    ->label('')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'h2', 'h3',
                                        'bulletList', 'orderedList',
                                        'link', 'blockquote',
                                        'redo', 'undo',
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Bio')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content('Biographie détaillée pour la presse.'),
                                Forms\Components\RichEditor::make('bio_longue_presse')
                                    ->label('')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'h2', 'h3',
                                        'bulletList', 'orderedList',
                                        'link', 'blockquote',
                                        'redo', 'undo',
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Booking')
                            ->icon('heroicon-o-currency-euro')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content('Cachet, défraiements, déplacements, hébergement...'),
                                Forms\Components\RichEditor::make('conditions_booking')
                                    ->label('')
                                    ->toolbarButtons([
                                        'bold', 'italic', 'underline',
                                        'h2', 'h3',
                                        'bulletList', 'orderedList',
                                        'link', 'blockquote',
                                        'redo', 'undo',
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Contacts')
                            ->icon('heroicon-o-phone')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content('Personnes à contacter pour le booking.'),
                                Forms\Components\Repeater::make('contacts')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Nom')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('role')
                                            ->label('Rôle')
                                            ->placeholder('Manager, Booking, etc.')
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Téléphone')
                                            ->tel()
                                            ->maxLength(50),
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email()
                                            ->maxLength(255),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Nouveau contact')
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->addActionLabel('Ajouter un contact'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Presse')
                            ->icon('heroicon-o-newspaper')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content('Articles de presse concernant le groupe.'),
                                Forms\Components\Repeater::make('revue_de_presse')
                                    ->label('')
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label('Titre de l\'article')
                                            ->required(),
                                        Forms\Components\TextInput::make('media')
                                            ->label('Nom du média')
                                            ->required(),
                                        Forms\Components\TextInput::make('url')
                                            ->label('URL')
                                            ->url(),
                                        Forms\Components\DatePicker::make('date')
                                            ->label('Date'),
                                    ])
                                    ->columns(2)
                                    ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'Nouvel article')
                                    ->collapsible()
                                    ->defaultItems(0)
                                    ->addActionLabel('Ajouter un article'),
                            ]),

                        Forms\Components\Tabs\Tab::make('Assets')
                            ->icon('heroicon-o-folder-open')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content('Fichiers téléchargeables par les pros autorisés (photos, PSD, AI, vidéos, PDF, Word, Excel...).'),
                                Forms\Components\FileUpload::make('photos_hd')
                                    ->label('')
                                    ->multiple()
                                    ->reorderable()
                                    ->disk('pro-files')
                                    ->directory('assets')
                                    ->visibility('public')
                                    ->maxSize(51200)
                                    ->acceptedFileTypes([
                                        'image/*',
                                        'video/*',
                                        'application/pdf',
                                        'application/postscript',
                                        'application/msword',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                        'application/octet-stream',
                                    ]),
                            ]),

                        Forms\Components\Tabs\Tab::make('Logos')
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content('Logos en formats vectoriels (SVG, PNG, AI...).'),
                                Forms\Components\FileUpload::make('logos_vectoriels')
                                    ->label('')
                                    ->multiple()
                                    ->reorderable()
                                    ->disk('pro-files')
                                    ->directory('logos')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/svg+xml', 'image/png', 'application/pdf', 'image/jpeg'])
                                    ->maxSize(10240),
                            ]),

                        Forms\Components\Tabs\Tab::make('Fiche tech.')
                            ->icon('heroicon-o-document-arrow-down')
                            ->schema([
                                Forms\Components\Placeholder::make('')
                                    ->content('La fiche technique est auto-générée depuis le module Espace Privé (équipement des musiciens + besoins techniques).'),
                                Forms\Components\Actions::make([
                                    Forms\Components\Actions\Action::make('download_tech_sheet')
                                        ->label('Télécharger le PDF')
                                        ->icon('heroicon-o-arrow-down-tray')
                                        ->url(route('tech-sheet.pdf'))
                                        ->openUrlInNewTab(),
                                ]),
                                Forms\Components\Placeholder::make('')
                                    ->content('Pour modifier le contenu, utilisez "Mon Équipement" et "Mes Besoins Techniques" dans Mon Espace.'),
                            ]),

                    ])
                    ->columnSpanFull()
                    ->contained(false)
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        // Save rich text content
        $richContentMap = [
            'hospitality-rider' => 'hospitality_rider',
            'bio-longue-presse' => 'bio_longue_presse',
            'conditions-booking' => 'conditions_booking',
        ];

        foreach ($richContentMap as $slug => $field) {
            $this->saveContentPage($slug, ['body' => $state[$field] ?? '']);
        }

        // Save contacts (multiple)
        $this->saveContentPage('contact-booking-direct', [
            'data' => $state['contacts'] ?? [],
        ]);

        // Save revue de presse
        $this->saveContentPage('revue-de-presse', [
            'data' => $state['revue_de_presse'] ?? [],
        ]);

        // Save files
        $this->saveContentPage('photos-hd', [
            'files' => $state['photos_hd'] ?? [],
        ]);

        $this->saveContentPage('logos-vectoriels', [
            'files' => $state['logos_vectoriels'] ?? [],
        ]);

        Notification::make()
            ->success()
            ->title('Tous les contenus ont été sauvegardés')
            ->send();
    }

    protected function saveContentPage(string $slug, array $data): void
    {
        $contentType = ProContentType::where('slug', $slug)->first();
        if (! $contentType) {
            return;
        }

        $page = ProContentPage::where('pro_content_type_id', $contentType->id)->first();
        if (! $page) {
            return;
        }

        $page->update(array_merge($data, ['updated_by' => auth()->id()]));
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
