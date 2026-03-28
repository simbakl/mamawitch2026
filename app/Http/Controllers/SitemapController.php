<?php

namespace App\Http\Controllers;

use App\Filament\Pages\PageManager;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Release;
use App\Models\StaticPage;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect();

        // Fixed pages — only include active ones
        foreach (PageManager::getActivePages() as $slug => $def) {
            $urls->push([
                'url' => $slug === 'home' ? url('/') : route($def['route']),
                'priority' => $def['priority'],
                'changefreq' => $def['changefreq'],
            ]);
        }

        // News articles (only if actus page is active)
        if (PageManager::isPageActive('actus')) {
            News::visible()
                ->select('slug', 'body', 'published_at', 'updated_at')
                ->latest('published_at')
                ->get()
                ->each(function ($article) use ($urls) {
                    if ($article->hasDetailPage()) {
                        $urls->push([
                            'url' => route('news.show', $article->slug),
                            'lastmod' => $article->updated_at->toW3cString(),
                            'priority' => '0.6',
                            'changefreq' => 'monthly',
                        ]);
                    }
                });
        }

        // Galleries (only if galerie page is active)
        if (PageManager::isPageActive('galerie')) {
            Gallery::where('is_published', true)
                ->select('slug', 'updated_at')
                ->get()
                ->each(function ($gallery) use ($urls) {
                    $urls->push([
                        'url' => route('gallery.show', $gallery->slug),
                        'lastmod' => $gallery->updated_at->toW3cString(),
                        'priority' => '0.5',
                        'changefreq' => 'monthly',
                    ]);
                });
        }

        // Releases (only if discographie page is active)
        if (PageManager::isPageActive('discographie')) {
            Release::where('is_published', true)
                ->select('slug', 'updated_at')
                ->get()
                ->each(function ($release) use ($urls) {
                    $urls->push([
                        'url' => route('release.show', $release->slug),
                        'lastmod' => $release->updated_at->toW3cString(),
                        'priority' => '0.6',
                        'changefreq' => 'yearly',
                    ]);
                });
        }

        // Static pages
        StaticPage::published()
            ->select('slug', 'updated_at')
            ->get()
            ->each(function ($page) use ($urls) {
                $urls->push([
                    'url' => url('/' . $page->slug),
                    'lastmod' => $page->updated_at->toW3cString(),
                    'priority' => '0.3',
                    'changefreq' => 'yearly',
                ]);
            });

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $entry) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($entry['url']) . '</loc>' . "\n";
            if (isset($entry['lastmod'])) {
                $xml .= '    <lastmod>' . $entry['lastmod'] . '</lastmod>' . "\n";
            }
            $xml .= '    <changefreq>' . $entry['changefreq'] . '</changefreq>' . "\n";
            $xml .= '    <priority>' . $entry['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }

        $xml .= '</urlset>';

        return response($xml, 200)
            ->header('Content-Type', 'application/xml');
    }
}
