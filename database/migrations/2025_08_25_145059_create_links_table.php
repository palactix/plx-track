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
        Schema::create('links', function (Blueprint $table) {
            // Primary identity
            $table->id();
            $table->string('short_code', 20)->unique();
            $table->text('original_url');
            
            // Ownership - nullable user_id for public links
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            
            // Basic link settings (from modal)
            $table->string('title', 500)->nullable();
            $table->text('description')->nullable();
            $table->boolean('custom_alias')->default(false);
            $table->boolean('meta_fetched')->default(false);
            
            // Tags (JSON for Laravel compatibility)
            $table->json('tags')->nullable();
            
            // SEO/Social (Phase 2 features)
            $table->string('meta_title', 500)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('og_image_url', 500)->nullable();
            
            // Security
            $table->string('password_hash')->nullable();
            $table->json('ip_whitelist')->nullable();
            $table->json('country_whitelist')->nullable();
            
            // Behavior
            $table->timestamp('expires_at')->nullable();
            $table->integer('redirect_type')->default(302);
            $table->boolean('is_active')->default(true);
            
            // Analytics
            $table->unsignedBigInteger('clicks_count')->default(0);
            $table->unsignedBigInteger('unique_clicks_count')->default(0);
            $table->timestamp('last_clicked_at')->nullable();
            
            // QR Code
            $table->string('qr_code_url', 500)->nullable();
            
            // UTM Parameters
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Indexes for performance
            $table->index(['user_id', 'created_at']);
            $table->index('expires_at');
            $table->index(['is_active', 'expires_at']);
            $table->index('last_clicked_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
