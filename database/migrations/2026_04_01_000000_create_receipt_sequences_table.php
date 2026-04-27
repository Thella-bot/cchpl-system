<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Create a counter table for sequential receipt numbers per financial year.
 *
 * This eliminates race conditions when two payments are verified
 * concurrently — the row for each FY is locked with SELECT FOR UPDATE.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('financial_year', 4)->unique();
            $table->unsignedInteger('last_sequence')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_sequences');
    }
};
