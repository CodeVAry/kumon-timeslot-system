<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Super Admin
        |--------------------------------------------------------------------------
        */

        Role::updateOrCreate(
            [
                'role_name' => 'Super Admin',
            ],
            [
                'description' =>
                    'Protected system role with complete access.',

                'is_active' => true,

                'superAdmin' => true,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        Role::updateOrCreate(
            [
                'role_name' => 'Admin',
            ],
            [
                'description' =>
                    'Centre administrator with administrative access.',

                'is_active' => true,

                'superAdmin' => false,
            ]
        );
    }
}
