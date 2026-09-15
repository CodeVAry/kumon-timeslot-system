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

                'is_active' =>
                    true,

                'superAdmin' =>
                    true,
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
                    'Centre administrator with operational access.',

                'is_active' =>
                    true,

                'superAdmin' =>
                    false,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | In Charge
        |--------------------------------------------------------------------------
        */

        Role::updateOrCreate(
            [
                'role_name' => 'In Charge',
            ],
            [
                'description' =>
                    'Centre in-charge user with the same operational access as Admin.',

                'is_active' =>
                    true,

                'superAdmin' =>
                    false,
            ]
        );
    }
}
