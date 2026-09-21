<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Generic ordered collections: stats, standards, facilities, contact rows,
        // socials, swatches, motion notes, project filters, form select options.
        Schema::create('content_collections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->json('schema')->nullable();
            $table->timestamps();
        });

        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_collection_id')->constrained()->cascadeOnDelete();
            $table->string('key')->nullable();
            $table->json('label')->nullable();
            $table->json('value')->nullable();
            $table->json('extra')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('url', 2048)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index(['content_collection_id', 'sort']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('content_collections');
    }
};
