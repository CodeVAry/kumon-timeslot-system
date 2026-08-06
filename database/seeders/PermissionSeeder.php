<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            [
                'name' => 'Roles',
                'key' => 'roles',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'print',
                ],
            ],
            [
                'name' => 'Permissions',
                'key' => 'permissions',
                'actions' => [
                    'view',
                    'edit',
                ],
            ],
            [
                'name' => 'Users',
                'key' => 'users',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'print',
                ],
            ],
            [
                'name' => 'Days',
                'key' => 'days',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],
            ],
            [
                'name' => 'Timeslots',
                'key' => 'timeslots',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'print',
                ],
            ],
            [
                'name' => 'Sections',
                'key' => 'sections',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'print',
                ],
            ],
            [
                'name' => 'Section Offerings',
                'key' => 'section_offerings',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'print',
                ],
            ],
            [
                'name' => 'Students',
                'key' => 'students',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'print',
                ],
            ],
            [
                'name' => 'Student Statuses',
                'key' => 'student_statuses',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],
            ],
            [
                'name' => 'Enrolments',
                'key' => 'enrolments',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                    'print',
                ],
            ],
            [
                'name' => 'Enrolment Statuses',
                'key' => 'enrolment_statuses',
                'actions' => [
                    'view',
                    'create',
                    'edit',
                    'delete',
                ],
            ],
            [
                'name' => 'Audit Logs',
                'key' => 'audit_logs',
                'actions' => [
                    'view',
                    'print',
                ],
            ],
        ];

        foreach ($modules as $module) {
            foreach ($module['actions'] as $action) {
                Permission::updateOrCreate(
                    [
                        'permission_key' =>
                            $module['key'] . '.' . $action,
                    ],
                    [
                        'permission_name' =>
                            ucfirst($action) . ' ' . $module['name'],

                        'module' => $module['name'],

                        'description' =>
                            'Allows the role to ' .
                            $action .
                            ' ' .
                            strtolower($module['name']) .
                            '.',

                        'is_active' => true,
                    ]
                );
            }
        }
    }
}
