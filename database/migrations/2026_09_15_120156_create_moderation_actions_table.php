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
        if (!Schema::hasTable('moderation_actions')) {
            Schema::create('moderation_actions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete();
                $table->morphs('moderatable');
                $table->enum('action', ['approve', 'reject', 'spam', 'escalate']);
                $table->text('reason');
                $table->json('context')->nullable();
                $table->timestamps();

                $table->index(['moderator_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_actions');
    }
};
