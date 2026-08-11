<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Provider-side transaction id for EcoCash webhook idempotency + tracking.
            // Note: existing 2024 migration already added `transaction_id`.
            // We keep this migration safe/idempotent.
            if (! Schema::hasColumn('payments', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('transaction_reference');
            }
        });
    }

    public function down(): void
    {
        // Keep rollback minimal; do not drop column if other code relies on it.
    }
};
