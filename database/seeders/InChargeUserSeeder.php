<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InChargeUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Get In Charge Role
        |--------------------------------------------------------------------------
        */

        $inChargeRole =
            Role::where(
                'role_name',
                'In Charge'
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
                'email' =>
                    'zartashia.kumonnorthhobart@gmail.com',
            ],
            [
                'name' =>
                    'Zartashia',

                'phone' =>
                    null,

                'role_id' =>
                    $inChargeRole->id,

                'password' =>
                    Hash::make(
                        'KNH123!'
                    ),
            ]
        );
    }
}
