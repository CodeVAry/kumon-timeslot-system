<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardians', function (Blueprint $table) {
            $table->id();

            /*
             * Optional parent login account.
             * One user account belongs to one guardian.
             */
            $table->foreignId('user_id')
                ->nullable()
                ->unique()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('first_name', 100);
            $table->string('last_name', 100);

            /*
             * Guardian contact information.
             * These are not unique.
             */
            $table->string('email', 150)
                ->nullable();

            $table->string('normalized_email', 150)
                ->nullable()
                ->index();

            $table->string('phone', 30)
                ->nullable();

            $table->string('normalized_phone', 30)
                ->nullable()
                ->index();

            $table->text('address')
                ->nullable();

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardians');
    }
};
