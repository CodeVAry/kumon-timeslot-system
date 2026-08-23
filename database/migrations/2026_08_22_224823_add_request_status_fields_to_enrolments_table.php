<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrolments', function (Blueprint $table) {

            $table->string('wishlist_status', 20)
                ->nullable()
                ->after('is_wishlist');

            $table->foreignId('requested_by_guardian_id')
                ->nullable()
                ->after('wishlist_status')
                ->constrained('guardians')
                ->nullOnDelete();

            $table->foreignId('reviewed_by_user_id')
                ->nullable()
                ->after('requested_by_guardian_id')
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('reviewed_at')
                ->nullable()
                ->after('reviewed_by_user_id');

            $table->text('wishlist_review_note')
                ->nullable()
                ->after('reviewed_at');
        });
    }


    public function down(): void
    {
        Schema::table('enrolments', function (Blueprint $table) {

            $table->dropForeign([
                'requested_by_guardian_id',
            ]);

            $table->dropForeign([
                'reviewed_by_user_id',
            ]);

            $table->dropColumn([
                'wishlist_status',
                'requested_by_guardian_id',
                'reviewed_by_user_id',
                'reviewed_at',
                'wishlist_review_note',
            ]);
        });
    }
};
