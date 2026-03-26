<?php

namespace App\Http\Controllers;

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

        // Static routes
        $urls->push(['url' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly']);
        $urls->push(['url' => route('concerts'), 'priority' => '0.8', 'changefreq' => 'weekly']);
        $urls->push(['url' => route('news.index'), 'priority' => '0.8', 'changefreq' => 'daily']);
        $urls->push(['url' => route('band'), 'priority' => '0.7', 'changefreq' => 'monthly']);
        $urls->push(['url' => route('gallery.index'), 'priority' => '0.6', 'changefreq' => 'monthly']);
        $urls->push(['url' => route('videos'), 'priority' => '0.6', 'changefreq' => 'monthly']);
        $urls->push(['url' => route('discography'), 'priority' => '0.7', 'changefreq' => 'monthly']);
        $urls->push(['url' => route('contact'), 'priority' => '0.5', 'changefreq' => 'yearly']);

        // News articles
        News::visible()->latest('published_at')->get()->each(function ($article) use ($urls) {
            if ($article->hasDetailPage()) {
                $urls->push([
                    'url' => route('news.show', $article->slug),
                    'lastmod' => $article->updated_at->toW3cString(),
                    'priority' => '0.6',
                    'changefreq' => 'monthly',
                ]);
            }
        });

        // Galleries
        Gallery::where('is_published', true)->get()->each(function ($gallery) use ($urls) {
            $urls->push([
                'url' => route('gallery.show', $gallery->slug),
                'lastmod' => $gallery->updated_at->toW3cString(),
                'priority' => '0.5',
                'changefreq' => 'monthly',
            ]);
        });

        // Releases
        Release::where('is_published', true)->get()->each(function ($release) use ($urls) {
            $urls->push([
                'url' => route('release.show', $release->slug),
                'lastmod' => $release->updated_at->toW3cString(),
                'priority' => '0.6',
                'changefreq' => 'yearly',
            ]);
        });

        // Static pages
        StaticPage::published()->get()->each(function ($page) use ($urls) {
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
