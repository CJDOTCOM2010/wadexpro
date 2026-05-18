<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'sqlite') {
            DB::statement('CREATE TEMPORARY TABLE personal_access_tokens_backup (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tokenable_type VARCHAR(255),
                tokenable_id VARCHAR(36),
                name VARCHAR(255),
                token VARCHAR(64),
                abilities TEXT,
                last_used_at DATETIME,
                expires_at DATETIME,
                created_at DATETIME,
                updated_at DATETIME
            )');
            DB::statement('INSERT INTO personal_access_tokens_backup SELECT * FROM personal_access_tokens');
            DB::statement('DROP TABLE personal_access_tokens');
            Schema::create('personal_access_tokens', function (Blueprint $table) {
                $table->id();
                $table->morphs('tokenable');
                $table->string('name');
                $table->string('token', 64)->unique();
                $table->text('abilities')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
            DB::statement('INSERT INTO personal_access_tokens (tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at)
                SELECT tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at FROM personal_access_tokens_backup');
            DB::statement('DROP TABLE personal_access_tokens_backup');
        } else {
            try {
                Schema::table('personal_access_tokens', function (Blueprint $table) {
                    $table->dropIndex('personal_access_tokens_tokenable_type_tokenable_id_index');
                });
            } catch (Exception $e) {
                // Index might not exist
            }

            DB::statement('ALTER TABLE personal_access_tokens ALTER COLUMN tokenable_id TYPE VARCHAR(36)');

            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->index(['tokenable_type', 'tokenable_id']);
            });
        }
    }

    public function down(): void
    {
        // Skip for SQLite - complex to reverse
    }
};
