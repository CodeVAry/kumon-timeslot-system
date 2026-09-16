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
                    !Schema::hasColumn(
                        'student_import_aliases',
                        'target_external_id'
                    )
                ) {

                    $table->string(
                        'target_external_id',
                        100
                    )
                        ->after(
                            'source_name'
                        );
                }
            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'student_import_aliases',
            function (Blueprint $table) {

                $table->dropColumn(
                    'target_external_id'
                );


                $table
                    ->foreignId(
                        'student_id'
                    )
                    ->nullable()
                    ->constrained(
                        'students'
                    )
                    ->cascadeOnDelete();
            }
        );
    }
};
