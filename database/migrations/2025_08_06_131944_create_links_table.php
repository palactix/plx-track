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
            $table->id();
            $table->string('short_code', 10)->unique();
            $table->text('original_url');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('session_id')->nullable()->index();
            $table->timestamp('claimed_at')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('custom_alias')->nullable()->unique();
            $table->string('password')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->json('utm_parameters')->nullable();
            $table->unsignedInteger('clicks_count')->default(0);
            $table->unsignedInteger('unique_click_count')->default(0);
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['session_id', 'created_at']);
            $table->index(['is_active', 'expires_at']);
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
