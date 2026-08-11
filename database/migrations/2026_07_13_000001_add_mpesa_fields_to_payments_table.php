<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('mpesa_checkout_request_id')->nullable()->after('transaction_reference');
            $table->string('mpesa_merchant_request_id')->nullable()->after('mpesa_checkout_request_id');
            // Receipt number returned by STK callback metadata
            $table->string('mpesa_receipt_number')->nullable()->after('mpesa_merchant_request_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn([
                'mpesa_checkout_request_id',
                'mpesa_merchant_request_id',
                'mpesa_receipt_number',
            ]);
        });
    }
};
