<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {

$driver = DB::getDriverName();

if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE payments
                MODIFY COLUMN status
                ENUM('pending','verified','rejected','voided')
                NOT NULL DEFAULT 'pending'
            ");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TYPE payments_status_enum ADD VALUE IF NOT EXISTS 'voided'");
        }

Schema::create('resignations', function (Blueprint $table) {
            $table->id();

$table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

$table->foreignId('membership_id')
                ->constrained()
                ->onDelete('cascade');

$table->enum('status', ['pending', 'acknowledged', 'cancelled'])
                ->default('pending');

$table->date('effective_date');

$table->string('reason_code', 60)->nullable();

$table->text('reason_notes')->nullable();

$table->decimal('balance_outstanding', 10, 2)->default(0);

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

$driver = DB::getDriverName();

if ($driver === 'mysql') {
            DB::table('payments')->where('status','voided')->update(['status' => 'rejected']);

DB::statement("
                ALTER TABLE payments
                MODIFY COLUMN status
                ENUM('pending','verified','rejected')
                NOT NULL DEFAULT 'pending'
            ");
        } elseif ($driver === 'pgsql') {

}

}
};
