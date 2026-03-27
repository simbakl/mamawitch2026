<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('concerts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->datetime('date');
            $table->string('venue');
            $table->string('city');
            $table->string('ticket_url')->nullable();
            $table->string('type')->default('concert');
            $table->string('poster')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'soldout', 'cancelled'])->default('upcoming');
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concerts');
    }
};
