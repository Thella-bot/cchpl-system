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
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE memberships
            MODIFY COLUMN status
            ENUM('pending','approved','rejected','suspended','expired','resigned')
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::statement("UPDATE memberships SET status = 'rejected' WHERE status = 'resigned'");

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("
            ALTER TABLE memberships
            MODIFY COLUMN status
            ENUM('pending','approved','rejected','suspended','expired')
            NOT NULL DEFAULT 'pending'
        ");
    }
};
