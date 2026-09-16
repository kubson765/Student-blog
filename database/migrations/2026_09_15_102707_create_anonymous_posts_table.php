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
        Schema::create('anonymous_posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');
            $table->string('category')->default('Ogólne');

            // Fingerprint (hash IP + User-Agent + secret)
            $table->string('fingerprint')->nullable()->index();

            // Technical metadata (deleted after a while)
            $table->string('ip_hash')->nullable();
            $table->string('user_agent_hash')->nullable();

            // Metadata
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('anonymous_posts');
    }
};
