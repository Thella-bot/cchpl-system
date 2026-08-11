<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->string('location')->nullable();
            $table->string('venue')->nullable();
            $table->string('image_path')->nullable();
            $table->integer('capacity')->nullable();
            $table->integer('spots_taken')->default(0);
            $table->date('registration_deadline')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('currency', 3)->default('M');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['membership_category_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
