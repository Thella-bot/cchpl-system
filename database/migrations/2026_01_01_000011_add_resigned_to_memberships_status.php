<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Adds 'resigned' to memberships.status enum so ResignationController
 * can write it when a resignation is acknowledged.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE memberships
                MODIFY COLUMN status
                ENUM('pending','approved','rejected','suspended','expired','resigned')
                NOT NULL DEFAULT 'pending'
            ");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TYPE memberships_status_enum ADD VALUE IF NOT EXISTS 'resigned'");
        }
        // SQLite stores enums as TEXT — no schema change needed.
    }

    public function down(): void
    {
        DB::statement("UPDATE memberships SET status = 'rejected' WHERE status = 'resigned'");

        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE memberships
                MODIFY COLUMN status
                ENUM('pending','approved','rejected','suspended','expired')
                NOT NULL DEFAULT 'pending'
            ");
        } elseif ($driver === 'pgsql') {
            // PostgreSQL does not support removing enum values directly.
            // Reverted rows above are sufficient.
        }
        // SQLite: no enum constraint to revert.
    }
};
