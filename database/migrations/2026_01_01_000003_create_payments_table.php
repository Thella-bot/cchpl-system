<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('provider', ['mpesa', 'ecocash']);
            $table->string('transaction_reference')->unique();
            $table->string('proof_file')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected', 'voided'])->default('pending');
            $table->text('verification_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};
