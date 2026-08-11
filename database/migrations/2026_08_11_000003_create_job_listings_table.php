<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('company_name');
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable(); // full-time, part-time, contract, etc.
            $table->string('salary_range')->nullable();
            $table->date('application_deadline')->nullable();
            $table->string('application_url')->nullable();
            $table->string('contact_email')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['membership_category_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
