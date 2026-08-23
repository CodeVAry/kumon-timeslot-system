<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_leaves', function (Blueprint $table) {

            /*
             * Existing admin-created leave records
             * must remain approved.
             */
            $table->string('status', 20)
                ->default('approved')
                ->after('student_id');


            /*
             * Parent who submitted the request.
             * Null means it was created directly by staff.
             */
            $table->foreignId('requested_by_guardian_id')
                ->nullable()
                ->after('status')
                ->constrained('guardians')
                ->nullOnDelete();


            /*
             * Admin/staff member who reviewed
             * the parent request.
             */
            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->after('requested_by_guardian_id')
                ->constrained('users')
                ->nullOnDelete();


            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by_user_id');
        });
    }


    public function down(): void
    {
        Schema::table('student_leaves', function (Blueprint $table) {

            $table->dropForeign([
                'requested_by_guardian_id',
            ]);

            $table->dropForeign([
                'reviewed_by_user_id',
            ]);

            $table->dropColumn([
                'status',
                'requested_by_guardian_id',
                'reviewed_by_user_id',
                'reviewed_at',
            ]);
        });
    }
};
