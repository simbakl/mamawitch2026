<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('releases', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['ep', 'album', 'single'])->default('ep');
            $table->string('cover')->nullable();
            $table->date('release_date')->nullable();
            $table->text('description')->nullable();
            $table->string('player_embed_url')->nullable();
            $table->text('credits')->nullable();
            $table->string('spotify_url')->nullable();
            $table->string('bandcamp_url')->nullable();
            $table->string('apple_music_url')->nullable();
            $table->string('deezer_url')->nullable();
            $table->string('soundcloud_url')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('releases');
    }
};
