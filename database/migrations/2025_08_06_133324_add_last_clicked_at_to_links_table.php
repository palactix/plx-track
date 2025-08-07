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
        Schema::table('links', function (Blueprint $table) {
            // Add last_clicked_at for quick sorting by recent activity
            $table->timestamp('last_clicked_at')->nullable()->after('clicks_count');
            $table->index('last_clicked_at'); // Index for performance when sorting by last clicked
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn('last_clicked_at');
        });
    }
};
