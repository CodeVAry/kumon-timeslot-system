<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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
    }
}
