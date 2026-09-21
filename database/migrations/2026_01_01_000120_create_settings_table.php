<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group')->default('general');
            $table->string('key')->unique();
            $table->string('label')->nullable();
            $table->string('type')->default('text');
            $table->boolean('is_translatable')->default(false);
            $table->json('value')->nullable();
            $table->json('options')->nullable();
            $table->string('hint')->nullable();
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();

            $table->index('group');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
