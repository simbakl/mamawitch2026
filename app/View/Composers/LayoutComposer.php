<?php

namespace App\View\Composers;

use App\Filament\Pages\PageManager;
use App\Models\SiteSetting;
use App\Models\StaticPage;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class LayoutComposer
{
    public function compose(View $view): void
    {
        // Batch-load all page active states (single operation, memoized)
        $activeStates = PageManager::loadActiveStates();

        // Build nav items — filtered by active state
        $allNavItems = [
            ['route' => 'home', 'label' => 'Accueil', 'page' => 'home'],
            ['route' => 'concerts', 'label' => 'Concerts', 'page' => 'concerts'],
            ['route' => 'news.index', 'label' => 'Actus', 'page' => 'actus'],
            ['route' => 'band', 'label' => 'Le Groupe', 'page' => 'le-groupe'],
            ['route' => 'gallery.index', 'label' => 'Galerie', 'page' => 'galerie'],
            ['route' => 'videos', 'label' => 'Vidéos', 'page' => 'videos'],
            ['route' => 'discography', 'label' => 'Discographie', 'page' => 'discographie'],
            ['route' => 'contact', 'label' => 'Contact', 'page' => 'contact'],
        ];

        $navItems = array_values(array_filter(
            $allNavItems,
            fn ($item) => $activeStates[$item['page']] ?? true
        ));

        // Static pages for menu and footer (cached for 1 hour)
        $staticMenuPages = Cache::remember('static_pages_menu', 3600, function () {
            return StaticPage::inMenu()->get();
        });

        $staticFooterPages = Cache::remember('static_pages_footer', 3600, function () {
            return StaticPage::inFooter()->get();
        });

        // Batch-load social settings (single DB query if not cached)
        $socialSettings = SiteSetting::getMany([
            'social_facebook',
            'social_instagram',
            'social_youtube',
            'social_twitter',
            'meta_description',
            'google_analytics_id',
        ]);

        $view->with([
            'navItems' => $navItems,
            'staticMenuPages' => $staticMenuPages,
            'staticFooterPages' => $staticFooterPages,
            'isProActive' => $activeStates['pro'] ?? true,
            'socialLinks' => [
                'facebook' => $socialSettings['social_facebook'],
                'instagram' => $socialSettings['social_instagram'],
                'youtube' => $socialSettings['social_youtube'],
                'twitter' => $socialSettings['social_twitter'],
            ],
            'defaultMetaDescription' => $socialSettings['meta_description'] ?? 'Mama Witch - Groupe de Hard Rock - Paris',
            'googleAnalyticsId' => $socialSettings['google_analytics_id'],
        ]);
    }
}
