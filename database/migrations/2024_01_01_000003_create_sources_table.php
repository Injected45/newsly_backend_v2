<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->string('rss_url');
            $table->string('website_url')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_breaking_source')->default(false);
            $table->integer('fetch_interval_seconds')->default(300);
            $table->timestamp('last_fetched_at')->nullable();
            $table->string('http_etag')->nullable();
            $table->string('http_last_modified')->nullable();
            $table->integer('consecutive_failures')->default(0);
            $table->timestamp('next_fetch_at')->nullable();
            $table->string('language', 10)->default('ar');
            $table->json('settings')->nullable(); // additional source settings
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('is_breaking_source');
            $table->index('next_fetch_at');
            $table->index(['country_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
    }
};



