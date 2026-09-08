<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'sub_sections',
            function (Blueprint $table) {

                $table->id();

                $table
                    ->foreignId('section_id')
                    ->constrained('sections')
                    ->cascadeOnUpdate()
                    ->restrictOnDelete();

                $table->string(
                    'sub_section_name',
                    100
                );

                $table
                    ->text('description')
                    ->nullable();

                $table
                    ->boolean('is_active')
                    ->default(true);

                $table->timestamps();


                /*
                |--------------------------------------------------------------------------
                | Prevent Duplicate Sub-section Names
                |--------------------------------------------------------------------------
                |
                | Example:
                | Math cannot have two "3A" sub-sections.
                |--------------------------------------------------------------------------
                */

                $table->unique([
                    'section_id',
                    'sub_section_name',
                ]);
            }
        );
    }


    public function down(): void
    {
        Schema::dropIfExists(
            'sub_sections'
        );
    }
};
