<?php

namespace Database\Seeders;

use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class InChargePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Admin Role
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
        | In Charge Role
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
        | Copy Admin Permissions
        |--------------------------------------------------------------------------
        |
        | In Charge receives exactly the same permissions
        | currently assigned to Admin.
        |
        */

        $permissionIds =
            $adminRole
                ->permissions()
                ->where(
                    'is_active',
                    true
                )
                ->pluck(
                    'permissions.id'
                )
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Sync In Charge Permissions
        |--------------------------------------------------------------------------
        */

        $inChargeRole
            ->permissions()
            ->sync(
                $permissionIds
            );
    }
}
