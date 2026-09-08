<?php

namespace Database\Seeders;

use App\Models\Admin\Section;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                'section_name' => 'English',
                'description' => 'English class',
                'is_active' => true,
            ],
            [
                'section_name' => 'Math',
                'description' => 'Math class with shared sub-section capacity.',
                'is_active' => true,
            ],
            [
                'section_name' => 'Interactive',
                'description' => 'Interactive class',
                'is_active' => true,
            ],
        ];

        foreach ($sections as $section) {
            Section::updateOrCreate(
                [
                    'section_name' => $section['section_name'],
                ],
                $section
            );
        }
    }
}
