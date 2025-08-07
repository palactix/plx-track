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
        Schema::table('clicks', function (Blueprint $table) {
            // Add essential indexes for common query patterns mentioned in feedback
            $table->index(['session_id', 'clicked_at']); // For session-based analytics
            $table->index(['ip_address', 'link_id']); // For unique visitor detection (same IP on same link)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clicks', function (Blueprint $table) {
            $table->dropIndex(['session_id', 'clicked_at']);
            $table->dropIndex(['ip_address', 'link_id']);
        });
    }
};
