<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Get Admin Role
        |--------------------------------------------------------------------------
        */

        $adminRole =
            Role::where(
                'role_name',
                'Admin'
            )
                ->where(
                    'superAdmin',
                    false
                )
                ->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Zartashia
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'zartashia@gmail.com',
            ],
            [
                'name' => 'Zartashia',

                'phone' => null,

                'role_id' =>
                    $adminRole->id,

                'password' =>
                    Hash::make(
                        'zartashia1234'
                    ),
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Carmel
        |--------------------------------------------------------------------------
        */

        User::updateOrCreate(
            [
                'email' => 'carmel@kumon.com',
            ],
            [
                'name' => 'Carmel',

                'phone' => null,

                'role_id' =>
                    $adminRole->id,

                'password' =>
                    Hash::make(
                        'carmel12345'
                    ),
            ]
        );
    }
}
