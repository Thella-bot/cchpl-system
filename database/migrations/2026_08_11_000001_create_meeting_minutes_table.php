<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meeting_minutes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('meeting_date');
            $table->string('location')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['membership_category_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meeting_minutes');
    }
};
