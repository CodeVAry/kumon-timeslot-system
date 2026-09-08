<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([

            /*
            |--------------------------------------------------------------------------
            | User Access
            |--------------------------------------------------------------------------
            */

            RoleSeeder::class,

            PermissionSeeder::class,

            SuperAdminUserSeeder::class,

            AdminUserSeeder::class,


            /*
            |--------------------------------------------------------------------------
            | Student Status
            |--------------------------------------------------------------------------
            */

            StudentStatusSeeder::class,


            /*
            |--------------------------------------------------------------------------
            | Centre Configuration
            |--------------------------------------------------------------------------
            */

            DaySeeder::class,

            SectionSeeder::class,

            SubSectionSeeder::class,

            SectionOfferingSeeder::class,
        ]);
    }
}
