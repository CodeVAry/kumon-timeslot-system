<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('section_offerings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('day_id')
                ->constrained('days')
                ->restrictOnDelete();

            $table->foreignId('section_id')
                ->constrained('sections')
                ->restrictOnDelete();

            $table->time('start_time');

            $table->unsignedSmallInteger('duration_minutes');

            $table->time('end_time');

            $table->unsignedSmallInteger('max_seats');

            $table->boolean('is_active')
                ->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_offerings');
    }
};
