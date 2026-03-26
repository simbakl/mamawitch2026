<?php

namespace Tests\Feature;

use App\Models\Concert;
use App\Models\Gallery;
use App\Models\GalleryPhoto;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\Release;
use App\Models\StaticPage;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RoleSeeder::class);
    }

    public function test_home_page_returns_200(): void
    {
        $this->get('/')->assertOk();
    }

    public function test_concerts_page_returns_200(): void
    {
        $this->get('/concerts')->assertOk();
    }

    public function test_news_index_returns_200(): void
    {
        $this->get('/actus')->assertOk();
    }

    public function test_news_show_returns_200(): void
    {
        $category = NewsCategory::create(['name' => 'Test', 'slug' => 'test']);
        $article = News::create([
            'title' => 'Article test',
            'slug' => 'article-test',
            'body' => '<p>Contenu</p>',
            'excerpt' => 'Extrait',
            'is_published' => true,
            'published_at' => now(),
            'news_category_id' => $category->id,
        ]);

        $this->get('/actus/article-test')->assertOk();
    }

    public function test_news_by_category_returns_200(): void
    {
        $category = NewsCategory::create(['name' => 'Live', 'slug' => 'live']);

        $this->get('/actus/categorie/live')->assertOk();
    }

    public function test_band_page_returns_200(): void
    {
        $this->get('/le-groupe')->assertOk();
    }

    public function test_gallery_index_returns_200(): void
    {
        $this->get('/galerie')->assertOk();
    }

    public function test_gallery_show_returns_200(): void
    {
        $gallery = Gallery::create([
            'title' => 'Concert 2026',
            'slug' => 'concert-2026',
            'is_published' => true,
        ]);

        $this->get('/galerie/concert-2026')->assertOk();
    }

    public function test_videos_page_returns_200(): void
    {
        $this->get('/videos')->assertOk();
    }

    public function test_discography_page_returns_200(): void
    {
        $this->get('/discographie')->assertOk();
    }

    public function test_release_show_returns_200(): void
    {
        $release = Release::create([
            'title' => 'EP Test',
            'slug' => 'ep-test',
            'type' => 'ep',
            'is_published' => true,
        ]);

        $this->get('/discographie/ep-test')->assertOk();
    }

    public function test_contact_page_returns_200(): void
    {
        $this->get('/contact')->assertOk();
    }

    public function test_static_page_returns_200(): void
    {
        StaticPage::create([
            'title' => 'Mentions légales',
            'slug' => 'mentions-legales',
            'body' => '<p>Contenu légal</p>',
            'is_published' => true,
        ]);

        $this->get('/mentions-legales')->assertOk();
    }

    public function test_unpublished_static_page_returns_404(): void
    {
        StaticPage::create([
            'title' => 'Brouillon',
            'slug' => 'brouillon',
            'body' => '<p>Draft</p>',
            'is_published' => false,
        ]);

        $this->get('/brouillon')->assertNotFound();
    }

    public function test_sitemap_returns_xml(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml');
    }

    public function test_home_page_has_meta_tags(): void
    {
        $response = $this->get('/');
        $response->assertSee('<meta property="og:site_name" content="Mama Witch">', false);
        $response->assertSee('<link rel="canonical"', false);
    }
}
