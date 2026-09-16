<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'student_import_aliases',
            function (Blueprint $table) {

                $table->string(
                    'source_name'
                )
                    ->unique()
                    ->after('id');


                $table->foreignId(
                    'student_id'
                )
                    ->after('source_name')
                    ->constrained('students')
                    ->cascadeOnDelete();
            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'student_import_aliases',
            function (Blueprint $table) {

                $table->dropForeign([
                    'student_id'
                ]);

                $table->dropUnique([
                    'source_name'
                ]);

                $table->dropColumn([
                    'source_name',
                    'student_id',
                ]);
            }
        );
    }
};
