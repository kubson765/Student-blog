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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();

            // Autor posta (nullable – po usunięciu konta post zostaje jako anonimowy)
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null');

            // Treść posta
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('content');

            $table->string('category')->default('Ogólne');

            // Status posta
            $table->enum('status', ['draft', 'published', 'archived'])
                ->default('draft');

            // Data publikacji (null = jeszcze nie opublikowany)
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            // Indeksy dla wydajności
            $table->index(['status', 'published_at']);
            $table->index('category');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
