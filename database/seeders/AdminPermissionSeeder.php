<?php

namespace Database\Seeders;

use App\Models\Admin\Permission;
use App\Models\Admin\Role;
use Illuminate\Database\Seeder;

class AdminPermissionSeeder extends Seeder
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
        | Admin Permission Keys
        |--------------------------------------------------------------------------
        |
        | These exactly match PermissionSeeder.
        |--------------------------------------------------------------------------
        */

        $permissionKeys = [

            /*
            |--------------------------------------------------------------------------
            | Attendance
            |--------------------------------------------------------------------------
            */

            'attendance.view',
            'attendance.create',
            'attendance.edit',
            'attendance.print',


            /*
            |--------------------------------------------------------------------------
            | Audit Logs
            |--------------------------------------------------------------------------
            */

            'audit_logs.view',
            'audit_logs.print',


            /*
            |--------------------------------------------------------------------------
            | Enrolments
            |--------------------------------------------------------------------------
            */

            'enrolments.view',
            'enrolments.create',
            'enrolments.edit',
            'enrolments.delete',
            'enrolments.print',


            /*
            |--------------------------------------------------------------------------
            | Leave Management
            |--------------------------------------------------------------------------
            */

            'leave.view',
            'leave.create',
            'leave.edit',
            'leave.delete',
            'leave.print',


            /*
            |--------------------------------------------------------------------------
            | Schedule
            |--------------------------------------------------------------------------
            */

            'schedule.view',
            'schedule.print',


            /*
            |--------------------------------------------------------------------------
            | Section Offerings
            |--------------------------------------------------------------------------
            */

            'section_offerings.view',
            'section_offerings.create',
            'section_offerings.edit',
            'section_offerings.delete',
            'section_offerings.print',


            /*
            |--------------------------------------------------------------------------
            | Students
            |--------------------------------------------------------------------------
            */

            'students.view',
            'students.create',
            'students.edit',
            'students.delete',
            'students.print',


            /*
            |--------------------------------------------------------------------------
            | Wishlist
            |--------------------------------------------------------------------------
            */

            'wishlist.view',
            'wishlist.create',
            'wishlist.edit',
        ];


        /*
        |--------------------------------------------------------------------------
        | Get Permission IDs
        |--------------------------------------------------------------------------
        */

        $permissionIds =
            Permission::whereIn(
                'permission_key',
                $permissionKeys
            )
                ->where(
                    'is_active',
                    true
                )
                ->pluck(
                    'id'
                )
                ->toArray();


        /*
        |--------------------------------------------------------------------------
        | Sync Admin Permissions
        |--------------------------------------------------------------------------
        */

        $adminRole
            ->permissions()
            ->sync(
                $permissionIds
            );
    }
}
