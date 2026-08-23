<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /*
    |--------------------------------------------------------------------------
    | Run Migration
    |--------------------------------------------------------------------------
    */

    public function up(): void
    {
        /*
         * These unique indexes were used by an older
         * version of the guardians table.
         *
         * New installations may already create email
         * and phone without unique indexes.
         *
         * Therefore only remove the indexes if they exist.
         */

        if ($this->indexExists(
            'guardians',
            'guardians_email_unique'
        )) {

            Schema::table(
                'guardians',
                function (Blueprint $table) {

                    $table->dropUnique(
                        'guardians_email_unique'
                    );
                }
            );
        }


        if ($this->indexExists(
            'guardians',
            'guardians_phone_unique'
        )) {

            Schema::table(
                'guardians',
                function (Blueprint $table) {

                    $table->dropUnique(
                        'guardians_phone_unique'
                    );
                }
            );
        }


        /*
         * Also check normalized fields in case an
         * earlier version made them unique.
         */

        if ($this->indexExists(
            'guardians',
            'guardians_normalized_email_unique'
        )) {

            Schema::table(
                'guardians',
                function (Blueprint $table) {

                    $table->dropUnique(
                        'guardians_normalized_email_unique'
                    );
                }
            );
        }


        if ($this->indexExists(
            'guardians',
            'guardians_normalized_phone_unique'
        )) {

            Schema::table(
                'guardians',
                function (Blueprint $table) {

                    $table->dropUnique(
                        'guardians_normalized_phone_unique'
                    );
                }
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Reverse Migration
    |--------------------------------------------------------------------------
    */

    public function down(): void
    {
        /*
         * Do not recreate the unique indexes.
         *
         * Guardian email and phone are intentionally
         * allowed to be duplicated because multiple
         * guardian records may use the same contact
         * information.
         */
    }


    /*
    |--------------------------------------------------------------------------
    | Check Index Exists
    |--------------------------------------------------------------------------
    */

    private function indexExists(
        string $table,
        string $indexName
    ): bool {
        $database =
            DB::connection()
                ->getDatabaseName();


        $result =
            DB::table(
                'information_schema.statistics'
            )
                ->where(
                    'table_schema',
                    $database
                )
                ->where(
                    'table_name',
                    $table
                )
                ->where(
                    'index_name',
                    $indexName
                )
                ->exists();


        return $result;
    }
};
