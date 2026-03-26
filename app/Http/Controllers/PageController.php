<?php

namespace App\Http\Controllers;

use App\Models\Concert;
use App\Models\ContactMessage;
use App\Models\Gallery;
use App\Models\Member;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Release;
use App\Models\SiteSetting;
use App\Models\StaticPage;
use App\Models\Video;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function home()
    {
        return view('pages.home', [
            'hero' => [
                'image' => SiteSetting::get('hero_image'),
                'title' => SiteSetting::get('hero_title', 'Mama Witch'),
                'subtitle' => SiteSetting::get('hero_subtitle'),
                'cta_text' => SiteSetting::get('hero_cta_text'),
                'cta_url' => SiteSetting::get('hero_cta_url'),
            ],
            'concerts' => Concert::published()->upcoming()->orderBy('date')->take(3)->get(),
            'news' => News::latestPublished()->take(3)->get(),
            'latestRelease' => Release::published()->orderByDesc('release_date')->first(),
        ]);
    }

    public function concerts()
    {
        $upcoming = Concert::published()->upcoming()->orderBy('date')->get();
        $past = Concert::published()->past()->orderByDesc('date')->get();

        return view('pages.concerts', compact('upcoming', 'past'));
    }

    public function newsIndex()
    {
        $news = News::latestPublished()->paginate(9);
        $categories = NewsCategory::withCount('news')->get();

        return view('pages.news.index', compact('news', 'categories'));
    }

    public function newsShow(string $slug)
    {
        $article = News::where('slug', $slug)->visible()->firstOrFail();

        return view('pages.news.show', compact('article'));
    }

    public function newsByCategory(string $slug)
    {
        $category = NewsCategory::where('slug', $slug)->firstOrFail();
        $news = News::where('news_category_id', $category->id)->latestPublished()->paginate(9);
        $categories = NewsCategory::withCount('news')->get();

        return view('pages.news.index', compact('news', 'categories', 'category'));
    }

    public function band()
    {
        $members = Member::orderBy('sort_order')->get();

        return view('pages.band', compact('members'));
    }

    public function galleryIndex()
    {
        $galleries = Gallery::where('is_published', true)
            ->withCount('photos')
            ->orderByDesc('date')
            ->get();

        return view('pages.gallery.index', compact('galleries'));
    }

    public function galleryShow(string $slug)
    {
        $gallery = Gallery::where('slug', $slug)
            ->where('is_published', true)
            ->with('photos')
            ->firstOrFail();

        return view('pages.gallery.show', compact('gallery'));
    }

    public function videos()
    {
        $videos = Video::where('is_published', true)->orderByDesc('published_at')->get();

        return view('pages.videos', compact('videos'));
    }

    public function discography()
    {
        $releases = Release::published()->with('tracks')->orderByDesc('release_date')->get();

        return view('pages.discography', compact('releases'));
    }

    public function releaseShow(string $slug)
    {
        $release = Release::where('slug', $slug)->published()->with('tracks')->firstOrFail();

        return view('pages.release', compact('release'));
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function contactSubmit(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'nullable|max:255',
            'message' => 'required|max:5000',
            'honeypot' => 'size:0',
        ]);

        unset($validated['honeypot']);

        ContactMessage::create($validated);

        return back()->with('success', 'Votre message a bien ete envoye. Nous vous repondrons dans les plus brefs delais.');
    }

    public function staticPage(string $slug)
    {
        $page = StaticPage::where('slug', $slug)->published()->firstOrFail();

        return view('pages.static', compact('page'));
    }
}
