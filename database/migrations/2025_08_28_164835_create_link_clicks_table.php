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
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->index();
            $table->text('user_agent')->nullable();
            $table->text('referrer')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->string('region')->nullable();
            $table->string('device_type', 50)->nullable();
            $table->string('device_name')->nullable();
            $table->string('browser', 50)->nullable();
            $table->string('browser_version', 20)->nullable();
            $table->string('platform', 50)->nullable();
            $table->string('platform_version', 20)->nullable();
            $table->boolean('is_mobile')->default(false);
            $table->boolean('is_tablet')->default(false);
            $table->boolean('is_desktop')->default(false);
            $table->boolean('is_bot')->default(false);
            $table->json('utm_parameters')->nullable();
            $table->timestamp('clicked_at');
            $table->timestamps();

            $table->index(['link_id', 'clicked_at']);
            $table->index(['country', 'clicked_at']);
            $table->index(['device_type', 'clicked_at']);
            $table->index(['browser', 'clicked_at']);
            $table->index(['is_bot', 'clicked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clicks');
    }
};
