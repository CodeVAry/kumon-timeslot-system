<?php

namespace Database\Seeders;

use App\Models\Admin\StudentStatus;
use Illuminate\Database\Seeder;

class StudentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Active
        |--------------------------------------------------------------------------
        */

        StudentStatus::updateOrCreate(
            [
                'status_name' => 'Active',
            ],
            [
                'color_code' => '#16a34a',
                'description' => 'Regular active student.',
                'is_active' => true,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | New Student
        |--------------------------------------------------------------------------
        */

        StudentStatus::updateOrCreate(
            [
                'status_name' => 'New Student',
            ],
            [
                'color_code' => '#eab308',
                'description' => 'Newly enrolled student.',
                'is_active' => true,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Free Trial
        |--------------------------------------------------------------------------
        */

        StudentStatus::updateOrCreate(
            [
                'status_name' => 'Free Trial',
            ],
            [
                'color_code' => '#f97316',
                'description' => 'Student currently attending a free trial.',
                'is_active' => true,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Vacation
        |--------------------------------------------------------------------------
        */

        StudentStatus::updateOrCreate(
            [
                'status_name' => 'Vacation',
            ],
            [
                'color_code' => '#0891b2',
                'description' => 'Student temporarily on leave or vacation.',
                'is_active' => true,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Disable Old Temporary Absence
        |--------------------------------------------------------------------------
        */

        StudentStatus::where(
            'status_name',
            'Temporary Absence'
        )->update([
            'is_active' => false,
        ]);
    }
}
