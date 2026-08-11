<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description');
            $table->string('provider')->nullable();
            $table->string('eligibility')->nullable();
            $table->string('benefit')->nullable();
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
        Schema::dropIfExists('scholarships');
    }
};
