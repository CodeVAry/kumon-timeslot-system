<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guardians', function (Blueprint $table) {

            if (!Schema::hasColumn('guardians', 'normalized_email')) {
                $table->string('normalized_email', 255)
                    ->nullable()
                    ->after('email');
            }

            if (!Schema::hasColumn('guardians', 'normalized_phone')) {
                $table->string('normalized_phone', 30)
                    ->nullable()
                    ->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {

            if (Schema::hasColumn('guardians', 'normalized_email')) {
                $table->dropColumn('normalized_email');
            }

            if (Schema::hasColumn('guardians', 'normalized_phone')) {
                $table->dropColumn('normalized_phone');
            }
        });
    }
};
