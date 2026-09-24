<?php

namespace Database\Seeders;

use App\Models\Admin\Section;
use App\Models\Admin\SubSection;
use Illuminate\Database\Seeder;

class SubSectionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Math Sub-sections
        |--------------------------------------------------------------------------
        */

        $math =
            Section::where(
                'section_name',
                'Math'
            )
                ->firstOrFail();


        foreach (
            [
                '3A',
                'B-D',
                'E+',
            ]
            as $name
        ) {

            SubSection::updateOrCreate(
                [
                    'section_id' =>
                        $math->id,

                    'sub_section_name' =>
                        $name,
                ],
                [
                    'description' =>
                        null,

                    'is_active' =>
                        true,
                ]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Interactive Sub-sections
        |--------------------------------------------------------------------------
        |
        | The client requested Interactive to be divided into:
        | - English
        | - Math
        |--------------------------------------------------------------------------
        */

        $interactive =
            Section::where(
                'section_name',
                'Interactive'
            )
                ->firstOrFail();


        foreach (
            [
                'English',
                'Math',
            ]
            as $name
        ) {

            SubSection::updateOrCreate(
                [
                    'section_id' =>
                        $interactive->id,

                    'sub_section_name' =>
                        $name,
                ],
                [
                    'description' =>
                        'Interactive ' . $name,

                    'is_active' =>
                        true,
                ]
            );
        }
    }
}
