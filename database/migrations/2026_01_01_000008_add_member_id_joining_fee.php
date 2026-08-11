<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('memberships', function (Blueprint $table) {

            $table->string('member_id', 30)->nullable()->unique()->after('id');

            $table->timestamp('suspended_at')->nullable()->after('expiry_date');

            $table->text('rejection_reason')->nullable()->after('suspended_at');
        });

        Schema::table('membership_categories', function (Blueprint $table) {

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
