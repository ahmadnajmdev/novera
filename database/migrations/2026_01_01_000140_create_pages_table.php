<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('slug');
            $table->json('title');
            $table->json('eyebrow')->nullable();
            $table->json('heading')->nullable();
            $table->json('intro')->nullable();
            $table->string('template')->default('builder');
            $table->boolean('is_system')->default(false);
            $table->boolean('dark_hero')->default(false);
            $table->foreignId('hero_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->foreignId('og_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('noindex')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
