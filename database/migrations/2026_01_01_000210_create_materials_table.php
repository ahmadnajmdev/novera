<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_groups', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('name');
            $table->string('background')->default('#ffffff');
            $table->string('foreground')->default('#131936');
            $table->string('muted')->default('#6C7490');
            $table->string('line')->default('rgba(19,25,54,.14)');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('slug');
            $table->foreignId('material_group_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('tag')->nullable();
            $table->json('blurb')->nullable();
            $table->json('body')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->json('seo_title')->nullable();
            $table->json('seo_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('material_specs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->json('label');
            $table->json('value');
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('material_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('concept_material', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concept_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('sort')->default(0);

            $table->unique(['concept_id', 'material_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('concept_material');
        Schema::dropIfExists('material_images');
        Schema::dropIfExists('material_specs');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('material_groups');
    }
};
