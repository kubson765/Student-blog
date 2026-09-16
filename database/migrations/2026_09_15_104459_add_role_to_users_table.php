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
        Schema::table('users', function (Blueprint $table) {

            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['user', 'moderator', 'admin'])->default('user')->after('email');
            };

            // Kiedy moderator został powołany (audyt)
            if (!Schema::hasColumn('users', 'moderator_since')) {
                $table->timestamp('moderator_since')->nullable();
            }
            // Kto nadał rolę (audyt)
            if (!Schema::hasColumn('users', 'role_assigned_by')) {
                $table->foreignId('role_assigned_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });

        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id();

            // Kto moderował
            $table->foreignId('moderator_id')->constrained('users')->cascadeOnDelete();

            // Co moderował (polimorficzne – post, komentarz, user)
            $table->morphs('moderatable'); // moderatable_type, moderatable_id

            // Jaka akcja
            $table->enum('action', [
                'approve',
                'reject',
                'flag',
                'escalate',      // przekazanie do admina
                'request_changes',
                'hide',
                'restore',
            ]);

            // Uzasadnienie (wymóg DSA – statement of reasons)
            $table->text('reason');

            // Kontekst (np. spam_score, report_count)
            $table->json('context')->nullable();

            // Czy admin zatwierdził (four-eyes principle)
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamp('reviewed_at')->nullable();

            $table->timestamps();

            $table->index(['moderator_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
