<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Role;
use App\Models\User;
use App\Models\Admin\Permission;
use Illuminate\Support\Facades\Hash;

class SuperAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where(
            'role_name',
            'Super Admin'
        )
            ->where('superAdmin', true)
            ->firstOrFail();

        /*
         * Assign every active permission to Super Admin.
         */
        $permissionIds = Permission::where(
            'is_active',
            true
        )
            ->pluck('id')
            ->toArray();

        $superAdminRole
            ->permissions()
            ->sync($permissionIds);

        /*
         * Create the Super Admin user.
         */
        User::updateOrCreate(
            [
                'email' => 'superadmin@kumon.local',
            ],
            [
                'name' => 'Super Admin',
                'phone' => null,
                'role_id' => $superAdminRole->id,
                'is_active' => true,
                'email_verified_at' => now(),
                'password' => Hash::make(
                    'SuperAdmin123!'
                ),
            ]
        );
    }
}
