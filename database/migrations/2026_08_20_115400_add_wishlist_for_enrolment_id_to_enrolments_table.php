<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrolments', function (Blueprint $table) {

            $table->foreignId('wishlist_for_enrolment_id')
                ->nullable()
                ->after('section_offering_id')
                ->constrained('enrolments')
                ->nullOnDelete();
        });
    }


    public function down(): void
    {
        Schema::table('enrolments', function (Blueprint $table) {

            $table->dropForeign([
                'wishlist_for_enrolment_id',
            ]);

            $table->dropColumn(
                'wishlist_for_enrolment_id'
            );
        });
    }
};
