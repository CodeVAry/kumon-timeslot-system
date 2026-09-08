<?php

namespace Database\Seeders;

use App\Models\Admin\Section;
use App\Models\Admin\SubSection;
use Illuminate\Database\Seeder;

class SubSectionSeeder extends Seeder
{
    public function run(): void
    {
        $math = Section::where(
            'section_name',
            'Math'
        )->firstOrFail();

        foreach ([
            '3A',
            'B-D',
            'E+',
        ] as $name) {

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
    }
}
