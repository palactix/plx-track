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
        Schema::create('click_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->unsignedInteger('total_clicks')->default(0);
            $table->unsignedInteger('unique_clicks')->default(0);
            $table->json('countries')->nullable();
            $table->json('devices')->nullable();
            $table->json('browsers')->nullable();
            $table->json('platforms')->nullable();
            $table->json('referrers')->nullable();
            $table->json('hourly_distribution')->nullable();
            $table->timestamps();

            $table->unique(['link_id', 'date']);
            $table->index(['date', 'total_clicks']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('click_analytics');
    }
};
