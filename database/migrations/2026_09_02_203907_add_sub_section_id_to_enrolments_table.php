<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'enrolments',
            function (Blueprint $table) {

                $table
                    ->foreignId('sub_section_id')
                    ->nullable()
                    ->after('section_offering_id')
                    ->constrained('sub_sections')
                    ->nullOnDelete();
            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'enrolments',
            function (Blueprint $table) {

                $table->dropForeign([
                    'sub_section_id',
                ]);

                $table->dropColumn(
                    'sub_section_id'
                );
            }
        );
    }
};
