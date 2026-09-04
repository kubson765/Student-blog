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
    Schema::create('interactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');

        // Pola pod relację polimorficzną (interactable_type i interactable_id)
        $table->morphs('interactable');

        $table->string('type'); // np. 'like', 'bookmark', 'rating'
        $table->integer('value')->nullable(); // opcjonalna wartość, np. ocena 1-5
        $table->timestamps();

        // Jeden użytkownik może dać tylko jedną interakcję danego typu do konkretnego obiektu
        $table->unique(['user_id', 'interactable_type', 'interactable_id', 'type'], 'user_interaction_unique');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interactions');
    }
};
