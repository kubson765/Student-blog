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
        // database/migrations/XXXX_create_reports_table.php
        Schema::create('reports', function (Blueprint $table) {
            $table->id();

            // Kto zgłosił (nullable – anonimowe zgłoszenia)
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete();

            // Co zgłosił
            $table->morphs('reportable');

            // Powód
            $table->enum('reason', [
                'spam',
                'harassment',
                'hate_speech',
                'sexual_content',
                'violence',
                'misinformation',
                'copyright',
                'other',
            ]);

            $table->text('description')->nullable();

            // Status
            $table->enum('status', ['pending', 'reviewing', 'resolved', 'dismissed'])
                ->default('pending');

            // Fingerprint dla zapobiegania nadużyciom (ten sam IP nie może spamować reportami)
            $table->string('reporter_fingerprint')->nullable()->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
