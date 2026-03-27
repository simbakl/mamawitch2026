<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_account_music_project', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pro_account_id')->constrained()->cascadeOnDelete();
            $table->foreignId('music_project_id')->constrained()->cascadeOnDelete();
            $table->unique(['pro_account_id', 'music_project_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_account_music_project');
    }
};
