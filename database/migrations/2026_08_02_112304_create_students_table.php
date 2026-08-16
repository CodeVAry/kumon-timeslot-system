<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('external_id', 50)->unique();
            $table->foreignId('student_status_id')->constrained('student_statuses')->restrictOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150);
            $table->string('phone', 30);
            $table->date('date_of_birth');
            $table->text('address');
            $table->boolean('can_leave_alone')->default(false);
            $table->text('notes')->nullable();
            $table->date('join_date');
            $table->boolean('is_active')->default(true);
            $table->date('inactive_since')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
