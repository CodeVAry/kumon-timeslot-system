<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'section_offering_sub_section',
            function (Blueprint $table) {

                $table->id();

                $table
                    ->foreignId(
                        'section_offering_id'
                    )
                    ->constrained(
                        'section_offerings'
                    )
                    ->cascadeOnDelete();

                $table
                    ->foreignId(
                        'sub_section_id'
                    )
                    ->constrained(
                        'sub_sections'
                    )
                    ->cascadeOnDelete();

                $table->timestamps();

                $table->unique(
                    [
                        'section_offering_id',
                        'sub_section_id',
                    ],
                    'offering_subsection_unique'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'section_offering_sub_section'
        );
    }
};
