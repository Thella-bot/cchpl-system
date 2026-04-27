<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Two changes in one migration:
 *
 * 1. Adds 'voided' to payments.status enum so VoidAbandonedPayments command
 *    can write it without a DB error. MySQL requires ALTER TABLE to change an
 *    enum; we use a raw query so it works across MySQL versions.
 *
 * 2. Creates the resignations table for the member resignation workflow
 *    (Bylaw 1.4 / CCHPL-MEM-003 Part A & B).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Extend payments.status enum ───────────────────────────────
        // Raw SQL required because Laravel's enum() migration helper
        // replaces, rather than appends, values in MySQL.
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE payments
                MODIFY COLUMN status
                ENUM('pending','verified','rejected','voided')
                NOT NULL DEFAULT 'pending'
            ");
        }

        // ── 2. Resignations table ─────────────────────────────────────────
        Schema::create('resignations', function (Blueprint $table) {
            $table->id();

            // The member submitting the resignation
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // Their membership at the time (so the Secretary knows which
            // membership to close, including any balance outstanding)
            $table->foreignId('membership_id')
                ->constrained()
                ->onDelete('cascade');

            // Lifecycle status (Part A → Part B of CCHPL-MEM-003)
            // pending   — member has submitted, Secretary not yet acted
            // acknowledged — Secretary has sent acknowledgement (Part B)
            // cancelled — member withdrew before acknowledgement
            $table->enum('status', ['pending', 'acknowledged', 'cancelled'])
                ->default('pending');

            // Requested effective date (member may specify a future date)
            $table->date('effective_date');

            // Reason code (from the template options in CCHPL-MEM-003)
            $table->string('reason_code', 60)->nullable();
            // career_change | financial | insufficient_value | personal | dissatisfied | other

            // Any free-text the member added
            $table->text('reason_notes')->nullable();

            // Balance outstanding at the time of resignation (M)
            $table->decimal('balance_outstanding', 10, 2)->default(0);

            // Secretary actions
            $table->foreignId('acknowledged_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->text('acknowledgement_notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('membership_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resignations');

        if (DB::getDriverName() === 'mysql') {
            DB::table('payments')->where('status','voided')->update(['status' => 'rejected']);
            
            DB::statement("
                ALTER TABLE payments
                MODIFY COLUMN status
                ENUM('pending','verified','rejected')
                NOT NULL DEFAULT 'pending'
            ");
        }
    }
};
