<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('guid')->nullable();
            $table->string('title', 500);
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('link', 2048);
            $table->string('image_url', 2048)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('fetched_at');
            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->string('language', 10)->nullable();
            $table->string('checksum', 64); // SHA256
            $table->string('author')->nullable();
            $table->json('tags')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
            
            // Unique constraint for deduplication
            $table->unique(['source_id', 'checksum']);
            
            // Performance indexes
            $table->index('published_at');
            $table->index('is_breaking');
            $table->index('is_featured');
            $table->index(['country_id', 'published_at']);
            $table->index(['category_id', 'published_at']);
            $table->index(['source_id', 'published_at']);
            $table->index(['is_breaking', 'published_at']);
            
            // Full-text search index (MySQL)
            $table->fullText(['title', 'summary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};


