<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_leaves', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->date('start_date');

            $table->date('expected_return_date');

            $table->date('actual_return_date')
                ->nullable();

            $table->boolean('returned_early')
                ->default(false);

            $table->string(
                'homework_requirement',
                30
            );

            $table->string(
                'reason',
                255
            )
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->foreignId('created_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'student_leaves'
        );
    }
};
