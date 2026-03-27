<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PageManager extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?string $navigationLabel = 'Gestion des pages';

    protected static ?string $title = 'Gestion des pages';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.page-manager';

    public ?array $data = [];

    /**
     * Fixed pages configuration: slug => [route name, label, priority, changefreq, can be disabled]
     */
    public static function getPageDefinitions(): array
    {
        return [
            'home' => [
                'route' => 'home',
                'label' => 'Accueil',
                'priority' => '1.0',
                'changefreq' => 'weekly',
                'can_disable' => false,
            ],
            'concerts' => [
                'route' => 'concerts',
                'label' => 'Concerts',
                'priority' => '0.8',
                'changefreq' => 'weekly',
                'can_disable' => true,
            ],
            'actus' => [
                'route' => 'news.index',
                'label' => 'Actualités',
                'priority' => '0.8',
                'changefreq' => 'daily',
                'can_disable' => true,
            ],
            'le-groupe' => [
                'route' => 'band',
                'label' => 'Le Groupe',
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'can_disable' => true,
            ],
            'galerie' => [
                'route' => 'gallery.index',
                'label' => 'Galerie',
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'can_disable' => true,
            ],
            'videos' => [
                'route' => 'videos',
                'label' => 'Vidéos',
                'priority' => '0.6',
                'changefreq' => 'monthly',
                'can_disable' => true,
            ],
            'discographie' => [
                'route' => 'discography',
                'label' => 'Discographie',
                'priority' => '0.7',
                'changefreq' => 'monthly',
                'can_disable' => true,
            ],
            'contact' => [
                'route' => 'contact',
                'label' => 'Contact',
                'priority' => '0.5',
                'changefreq' => 'yearly',
                'can_disable' => true,
            ],
            'pro' => [
                'route' => 'pro.request',
                'label' => 'Espace Pro',
                'priority' => '0.4',
                'changefreq' => 'monthly',
                'can_disable' => true,
            ],
        ];
    }

    /**
     * Memoized active states for the current request.
     */
    protected static ?array $activeStates = null;

    /**
     * Load all page active states in a single batch.
     */
    public static function loadActiveStates(): array
    {
        if (static::$activeStates !== null) {
            return static::$activeStates;
        }

        $definitions = static::getPageDefinitions();
        $keys = [];
        foreach ($definitions as $slug => $def) {
            if ($def['can_disable']) {
                $keys[] = "page_active_{$slug}";
            }
        }

        $values = SiteSetting::getMany($keys);

        static::$activeStates = [];
        foreach ($definitions as $slug => $def) {
            if (! $def['can_disable']) {
                static::$activeStates[$slug] = true;
            } else {
                static::$activeStates[$slug] = (bool) ($values["page_active_{$slug}"] ?? '1');
            }
        }

        return static::$activeStates;
    }

    /**
     * Check if a page is active (enabled).
     */
    public static function isPageActive(string $slug): bool
    {
        return static::loadActiveStates()[$slug] ?? true;
    }

    /**
     * Get all active page definitions.
     */
    public static function getActivePages(): array
    {
        $states = static::loadActiveStates();

        return array_filter(
            static::getPageDefinitions(),
            fn (string $slug) => $states[$slug] ?? true,
            ARRAY_FILTER_USE_KEY
        );
    }

    public function mount(): void
    {
        $data = [];
        foreach (static::getPageDefinitions() as $slug => $def) {
            if ($def['can_disable']) {
                $data["page_active_{$slug}"] = (bool) SiteSetting::get("page_active_{$slug}", '1');
            }
        }
        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        $fields = [];

        foreach (static::getPageDefinitions() as $slug => $def) {
            if (! $def['can_disable']) {
                continue;
            }

            $fields[] = Forms\Components\Toggle::make("page_active_{$slug}")
                ->label($def['label'])
                ->helperText($this->getHelperText($slug))
                ->default(true)
                ->inline(false);
        }

        return $form
            ->schema([
                Forms\Components\Section::make('Pages du site')
                    ->description('Activez ou désactivez les pages publiques du site. Les pages désactivées ne seront plus accessibles, disparaîtront du menu de navigation, de la page d\'accueil et du sitemap.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema($fields),
                    ]),
                Forms\Components\Placeholder::make('info')
                    ->label('')
                    ->content('La page Accueil est toujours active et ne peut pas être désactivée.')
                    ->extraAttributes(['class' => 'text-sm text-gray-500']),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            if (str_starts_with($key, 'page_active_')) {
                SiteSetting::set($key, $value ? '1' : '0');
            }
        }

        Notification::make()
            ->title('Pages mises à jour')
            ->body('Les modifications sont effectives immédiatement.')
            ->success()
            ->send();
    }

    protected function getHelperText(string $slug): string
    {
        return match ($slug) {
            'concerts' => 'Page des concerts + section "Prochains Concerts" en accueil',
            'actus' => 'Page des actualités + section "Actualités" en accueil',
            'discographie' => 'Page discographie + section "Dernière Sortie" en accueil',
            'galerie' => 'Page galerie photos',
            'videos' => 'Page vidéos',
            'le-groupe' => 'Page de présentation du groupe',
            'contact' => 'Page et formulaire de contact',
            'pro' => 'Espace Pro (demande d\'accès, dashboard pro, streaming audio)',
            default => '',
        };
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }
}
