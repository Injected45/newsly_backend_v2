<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fetch_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained()->cascadeOnDelete();
            $table->string('request_url', 2048);
            $table->integer('http_status')->nullable();
            $table->integer('runtime_ms')->nullable();
            $table->string('etag_received')->nullable();
            $table->string('last_modified_received')->nullable();
            $table->integer('response_size_bytes')->nullable();
            $table->integer('articles_found')->default(0);
            $table->integer('articles_created')->default(0);
            $table->integer('articles_skipped')->default(0);
            $table->enum('status', ['success', 'not_modified', 'error', 'timeout'])->default('success');
            $table->text('error_message')->nullable();
            $table->json('response_headers')->nullable();
            $table->timestamp('created_at');
            
            $table->index('source_id');
            $table->index('status');
            $table->index('created_at');
            $table->index(['source_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fetch_logs');
    }
};


