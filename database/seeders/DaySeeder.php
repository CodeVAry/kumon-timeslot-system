<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin\Day;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            [
                'day_name' => 'Monday',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'day_name' => 'Tuesday',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'day_name' => 'Wednesday',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'day_name' => 'Thursday',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'day_name' => 'Friday',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'day_name' => 'Saturday',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'day_name' => 'Sunday',
                'sort_order' => 7,
                'is_active' => true,
            ],
        ];

        foreach ($days as $day) {
            Day::updateOrCreate(
                [
                    'day_name' => $day['day_name'],
                ],
                [
                    'sort_order' => $day['sort_order'],
                    'is_active' => $day['is_active'],
                ]
            );
        }
    }
}
