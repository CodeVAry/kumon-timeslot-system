<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            $table->dropUnique('guardians_email_unique');
            $table->dropUnique('guardians_phone_unique');
        });

        Schema::table('guardians', function (Blueprint $table) {
            $table->string('phone', 30)
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        /*
         * Rolling this migration back could fail if duplicate
         * emails or phone numbers have already been saved.
         */
        Schema::table('guardians', function (Blueprint $table) {
            $table->unique(
                'email',
                'guardians_email_unique'
            );

            $table->unique(
                'phone',
                'guardians_phone_unique'
            );
        });
    }
};
