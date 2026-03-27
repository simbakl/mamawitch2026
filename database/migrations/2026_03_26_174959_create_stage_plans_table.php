<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stage_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('Plan de scène principal');
            $table->json('elements')->nullable();
            $table->integer('stage_width')->default(800);
            $table->integer('stage_depth')->default(500);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stage_plans');
    }
};
