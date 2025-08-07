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
        Schema::create('claimable_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedInteger('links_count')->default(0);
            $table->timestamp('first_link_created')->nullable();
            $table->timestamp('last_activity')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->boolean('claim_prompted')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['session_id', 'claimed_at']);
            $table->index(['user_id', 'claimed_at']);
            $table->index(['claim_prompted', 'links_count']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claimable_sessions');
    }
};
