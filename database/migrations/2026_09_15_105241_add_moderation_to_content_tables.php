<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabele, które mają otrzymać kolumny moderacji
     */
    protected array $tables = ['posts', 'comments'];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'moderation_status')) {
                    $table->enum('moderation_status', [
                        'pending',
                        'approved',
                        'rejected',
                        'spam',
                        'hidden',
                    ])->default('pending');
                }

                if (!Schema::hasColumn($tableName, 'moderation_reason')) {
                    $table->text('moderation_reason')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'moderated_at')) {
                    $table->timestamp('moderated_at')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'moderated_by')) {
                    $table->foreignId('moderated_by')
                        ->nullable()
                        ->constrained('users')
                        ->onDelete('set null');
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'moderated_by')) {
                    $table->dropForeign(['moderated_by']);
                    $table->dropColumn('moderated_by');
                }
                if (Schema::hasColumn($tableName, 'moderated_at')) {
                    $table->dropColumn('moderated_at');
                }
                if (Schema::hasColumn($tableName, 'moderation_reason')) {
                    $table->dropColumn('moderation_reason');
                }
                if (Schema::hasColumn($tableName, 'moderation_status')) {
                    $table->dropColumn('moderation_status');
                }
            });
        }
    }
};
