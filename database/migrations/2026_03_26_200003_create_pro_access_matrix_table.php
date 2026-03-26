<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pro_access_matrix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pro_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pro_content_type_id')->constrained()->cascadeOnDelete();
            $table->unique(['pro_type_id', 'pro_content_type_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pro_access_matrix');
    }
};
