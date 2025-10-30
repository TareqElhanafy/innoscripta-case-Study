<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // foreign keys
            $table->foreignId('source_id')->constrained('sources')->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('authors')->nullOnDelete();

            // Article data
            $table->string('external_id')->nullable(); // provider internal id
            $table->string('url')->unique();
            $table->string('title');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_url')->nullable();

            // Category handling
            $table->string('provider_category')->nullable()->index();

            // meta data
            $table->string('language', 8)->default('en');
            $table->timestamp('published_at')->index();
            $table->string('checksum')->unique(); // prevent duplication
            $table->json('metadata')->nullable(); // raw data from api response

            // add timestamps only once
            $table->timestamps();

            // Indexes
            $table->index(['source_id', 'published_at']);
            $table->unique(['source_id', 'external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
