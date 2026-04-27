<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds:
 *   memberships.member_id       — CCHPL-PRO-2025-001 format (Welcome Pack § 2, Certificate CCHPL-MEM-002)
 *   memberships.suspended_at    — timestamp when auto-suspension applied (Bylaws 1.3)
 *   membership_categories.joining_fee — one-time joining fee (Bylaws 1.1)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            // Member ID — assigned on approval, unique across all memberships
            $table->string('member_id', 30)->nullable()->unique()->after('id');

            // Suspension timestamp (Bylaws 1.3 — non-payment 6+ months)
            $table->timestamp('suspended_at')->nullable()->after('expiry_date');

            // Rejection reason — store alongside status for auditability
            $table->text('rejection_reason')->nullable()->after('suspended_at');
        });

        Schema::table('membership_categories', function (Blueprint $table) {
            // One-time joining fee (Bylaws 1.1 — where set by EC)
            // Null means no joining fee applies for this category.
            $table->decimal('joining_fee', 10, 2)->nullable()->after('annual_fee');
        });
    }

    public function down(): void
    {
        Schema::table('memberships', function (Blueprint $table) {
            $table->dropColumn(['member_id', 'suspended_at', 'rejection_reason']);
        });

        Schema::table('membership_categories', function (Blueprint $table) {
            $table->dropColumn('joining_fee');
        });
    }
};
