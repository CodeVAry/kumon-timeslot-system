<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('email', 150)->nullable()->change();
            $table->string('phone', 30)->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->date('join_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('email', 150)->nullable(false)->change();
            $table->string('phone', 30)->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->date('join_date')->nullable(false)->change();
        });
    }
};
