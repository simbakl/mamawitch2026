<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Concerts: filtered by is_published + status, ordered by date
        Schema::table('concerts', function (Blueprint $table) {
            $table->index(['is_published', 'date']);
            $table->index('status');
        });

        // News: filtered by is_published + published_at
        Schema::table('news', function (Blueprint $table) {
            $table->index(['is_published', 'published_at']);
        });

        // Releases: filtered by is_published, ordered by release_date
        Schema::table('releases', function (Blueprint $table) {
            $table->index(['is_published', 'release_date']);
        });

        // Videos: filtered by is_published, ordered by published_at
        Schema::table('videos', function (Blueprint $table) {
            $table->index(['is_published', 'published_at']);
        });

        // Galleries: filtered by is_published, ordered by date
        Schema::table('galleries', function (Blueprint $table) {
            $table->index(['is_published', 'date']);
        });

        // Static pages: filtered by is_published + show_in_menu/footer
        Schema::table('static_pages', function (Blueprint $table) {
            $table->index(['is_published', 'show_in_menu', 'menu_order']);
            $table->index(['is_published', 'show_in_footer', 'menu_order']);
        });

        // Members: ordered by sort_order
        Schema::table('members', function (Blueprint $table) {
            $table->index('sort_order');
        });

    }

    public function down(): void
    {
        Schema::table('concerts', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'date']);
            $table->dropIndex(['status']);
        });

        Schema::table('news', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'published_at']);
        });

        Schema::table('releases', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'release_date']);
        });

        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'published_at']);
        });

        Schema::table('galleries', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'date']);
        });

        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropIndex(['is_published', 'show_in_menu', 'menu_order']);
            $table->dropIndex(['is_published', 'show_in_footer', 'menu_order']);
        });

        Schema::table('members', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });

    }
};
