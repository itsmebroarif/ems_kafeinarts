<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The voucher_id column type needs to be changed from unsignedBigInteger (foreignId)
        // to char(36) to match event_vouchers.id (UUID).
        // This is handled manually in the MySQL production database.
        // The migration records the change for version control but the actual type change
        // was applied directly to the database via PHP script.
    }

    /**
     * Reverse the migrations.
     *
     * Note: SQLite does not support dropping foreign keys by name in the test driver.
     * This down() method is kept for compatibility but may not fully reverse the migration
     * on SQLite. The up() migration has already been applied to the production MySQL database.
     */
    public function down(): void
    {
        // No-op for SQLite compatibility; the up() migration is intended for MySQL production.
        // To manually reverse: drop the foreign key and revert the column type.
    }
};
