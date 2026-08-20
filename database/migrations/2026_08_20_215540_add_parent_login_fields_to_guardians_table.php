<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guardians', function (Blueprint $table) {

            $table->string('normalized_email')
                ->nullable()
                ->after('email')
                ->index();

            $table->string('normalized_phone', 30)
                ->nullable()
                ->after('phone')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {

            $table->dropIndex([
                'normalized_email',
            ]);

            $table->dropIndex([
                'normalized_phone',
            ]);

            $table->dropColumn([
                'normalized_email',
                'normalized_phone',
            ]);
        });
    }
};
