<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'student_leaves',
            function (Blueprint $table) {

                $table->boolean(
                    'is_actioned'
                )
                    ->default(false)
                    ->after('returned_early');


                $table->timestamp(
                    'actioned_at'
                )
                    ->nullable()
                    ->after('is_actioned');


                $table->foreignId(
                    'actioned_by_user_id'
                )
                    ->nullable()
                    ->after('actioned_at')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        );
    }


    public function down(): void
    {
        Schema::table(
            'student_leaves',
            function (Blueprint $table) {

                $table->dropConstrainedForeignId(
                    'actioned_by_user_id'
                );


                $table->dropColumn([
                    'is_actioned',
                    'actioned_at',
                ]);
            }
        );
    }
};
