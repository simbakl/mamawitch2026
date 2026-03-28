<?php

namespace Tests\Unit;

use App\Models\Concert;
use App\Models\News;
use App\Models\NewsCategory;
use App\Models\ProAccount;
use App\Models\ProType;
use App\Models\Release;
use App\Models\StaticPage;
use App\Models\Track;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsTest extends TestCase
{
    use RefreshDatabase;

    // --- Concert ---

    public function test_concert_upcoming_scope(): void
    {
        Concert::create([
            'title' => 'Futur',
            'date' => now()->addMonth(),
            'venue' => 'Salle',
            'city' => 'Paris',
            'is_published' => true,
        ]);
        Concert::create([
            'title' => 'Passé',
            'date' => now()->subMonth(),
            'venue' => 'Salle',
            'city' => 'Paris',
            'is_published' => true,
        ]);

        $this->assertCount(1, Concert::upcoming()->get());
        $this->assertCount(1, Concert::past()->get());
    }

    public function test_concert_published_scope(): void
    {
        Concert::create(['title' => 'Public', 'date' => now(), 'venue' => 'V', 'city' => 'C', 'is_published' => true]);
        Concert::create(['title' => 'Draft', 'date' => now(), 'venue' => 'V', 'city' => 'C', 'is_published' => false]);

        $this->assertCount(1, Concert::published()->get());
    }

    // --- News ---

    public function test_news_visible_scope(): void
    {
        $cat = NewsCategory::create(['name' => 'Test', 'slug' => 'test']);

        News::create(['title' => 'Visible', 'slug' => 'visible', 'excerpt' => 'E', 'is_published' => true, 'published_at' => now()->subHour(), 'news_category_id' => $cat->id]);
        News::create(['title' => 'Future', 'slug' => 'future', 'excerpt' => 'E', 'is_published' => true, 'published_at' => now()->addDay(), 'news_category_id' => $cat->id]);
        News::create(['title' => 'Draft', 'slug' => 'draft', 'excerpt' => 'E', 'is_published' => false, 'news_category_id' => $cat->id]);

        $this->assertCount(1, News::visible()->get());
    }

    public function test_news_has_detail_page(): void
    {
        $cat = NewsCategory::create(['name' => 'T', 'slug' => 't']);

        $withBody = News::create(['title' => 'Full', 'slug' => 'full', 'excerpt' => 'E', 'body' => '<p>Content</p>', 'is_published' => true, 'news_category_id' => $cat->id]);
        $withoutBody = News::create(['title' => 'Short', 'slug' => 'short', 'excerpt' => 'E', 'is_published' => true, 'news_category_id' => $cat->id]);

        $this->assertTrue($withBody->hasDetailPage());
        $this->assertFalse($withoutBody->hasDetailPage());
    }

    // --- StaticPage ---

    public function test_static_page_in_menu_scope(): void
    {
        StaticPage::create(['title' => 'In Menu', 'slug' => 'in-menu', 'body' => 'B', 'is_published' => true, 'show_in_menu' => true, 'menu_order' => 1]);
        StaticPage::create(['title' => 'Not Menu', 'slug' => 'not-menu', 'body' => 'B', 'is_published' => true, 'show_in_menu' => false]);
        StaticPage::create(['title' => 'Draft Menu', 'slug' => 'draft', 'body' => 'B', 'is_published' => false, 'show_in_menu' => true]);

        $this->assertCount(1, StaticPage::inMenu()->get());
    }

    public function test_static_page_in_footer_scope(): void
    {
        StaticPage::create(['title' => 'Footer', 'slug' => 'footer', 'body' => 'B', 'is_published' => true, 'show_in_footer' => true]);
        StaticPage::create(['title' => 'No Footer', 'slug' => 'no-footer', 'body' => 'B', 'is_published' => true, 'show_in_footer' => false]);

        $this->assertCount(1, StaticPage::inFooter()->get());
    }

    // --- Release ---

    public function test_release_has_tracks(): void
    {
        $release = Release::create(['title' => 'EP', 'slug' => 'ep', 'type' => 'ep', 'is_published' => true]);
        Track::create(['release_id' => $release->id, 'title' => 'Track 1', 'track_number' => 1]);
        Track::create(['release_id' => $release->id, 'title' => 'Track 2', 'track_number' => 2]);

        $this->assertCount(2, $release->tracks);
    }

    // --- ProAccount ---

    public function test_pro_account_full_name(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\ProContentSeeder::class);

        $account = ProAccount::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@test.com',
            'structure' => 'Test',
            'pro_type_id' => ProType::first()->id,
        ]);

        $this->assertEquals('Jean Dupont', $account->full_name);
    }

    public function test_pro_account_scopes(): void
    {
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\ProContentSeeder::class);

        $proType = ProType::first();

        ProAccount::create(['first_name' => 'A', 'last_name' => 'A', 'email' => 'a@t.com', 'structure' => 'T', 'pro_type_id' => $proType->id, 'status' => 'pending']);
        ProAccount::create(['first_name' => 'B', 'last_name' => 'B', 'email' => 'b@t.com', 'structure' => 'T', 'pro_type_id' => $proType->id, 'status' => 'approved']);
        ProAccount::create(['first_name' => 'C', 'last_name' => 'C', 'email' => 'c@t.com', 'structure' => 'T', 'pro_type_id' => $proType->id, 'status' => 'rejected']);
        ProAccount::create(['first_name' => 'D', 'last_name' => 'D', 'email' => 'd@t.com', 'structure' => 'T', 'pro_type_id' => $proType->id, 'status' => 'invited']);

        $this->assertCount(1, ProAccount::pending()->get());
        $this->assertCount(1, ProAccount::approved()->get());
        $this->assertCount(2, ProAccount::active()->get()); // approved + invited
    }
}
