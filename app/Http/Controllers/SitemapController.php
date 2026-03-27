<?php

namespace App\Http\Controllers;

use App\Filament\Pages\PageManager;
use App\Models\Concert;
use App\Models\Gallery;
use App\Models\News;
use App\Models\Release;
use App\Models\StaticPage;
use Illuminate\Http\Response;

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

        // News articles (only if actus page is active) — select only needed columns
        if (PageManager::isPageActive('actus')) {
            News::visible()
                ->select('slug', 'body', 'published_at', 'updated_at')
                ->latest('published_at')
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
            ->each(function ($page) use ($urls) {
                $urls->push([
                    'url' => url('/' . $page->slug),
                    'lastmod' => $page->updated_at->toW3cString(),
                    'priority' => '0.3',
                    'changefreq' => 'yearly',
                ]);
            });

        $content = view('sitemap', ['urls' => $urls])->render();

        return response($content, 200)
            ->header('Content-Type', 'application/xml');
    }
}
