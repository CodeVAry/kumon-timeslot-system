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

                if (
                    !Schema::hasColumn(
                        'student_import_aliases',
                        'source_name'
                    )
                ) {

                    $table
                        ->string(
                            'source_name',
                            255
                        )
                        ->unique()
                        ->after('id');
                }


                if (
                    !Schema::hasColumn(
                        'student_import_aliases',
                        'student_id'
                    )
                ) {

                    $table
                        ->foreignId(
                            'student_id'
                        )
                        ->after('source_name')
                        ->constrained('students')
                        ->cascadeOnDelete();
                }
            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'student_import_aliases',
            function (Blueprint $table) {

                if (
                    Schema::hasColumn(
                        'student_import_aliases',
                        'student_id'
                    )
                ) {

                    $table->dropForeign([
                        'student_id',
                    ]);

                    $table->dropColumn(
                        'student_id'
                    );
                }


                if (
                    Schema::hasColumn(
                        'student_import_aliases',
                        'source_name'
                    )
                ) {

                    $table->dropUnique([
                        'source_name',
                    ]);

                    $table->dropColumn(
                        'source_name'
                    );
                }
            }
        );
    }
};
