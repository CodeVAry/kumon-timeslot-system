<?php

namespace Database\Seeders;

use App\Models\Admin\Day;
use App\Models\Admin\Section;
use App\Models\Admin\SectionOffering;
use App\Models\Admin\SubSection;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SectionOfferingSeeder extends Seeder
{
    public function run(): void
    {
        $english = Section::where(
            'section_name',
            'English'
        )->firstOrFail();

        $math = Section::where(
            'section_name',
            'Math'
        )->firstOrFail();

        $interactive = Section::where(
            'section_name',
            'Interactive'
        )->firstOrFail();


        /*
        |--------------------------------------------------------------------------
        | Interactive Sub-sections
        |--------------------------------------------------------------------------
        |
        | Interactive has one combined capacity of five seats. Every student
        | enrolled in an Interactive offering must be identified as either
        | English or Math. updateOrCreate keeps this seeder safe to run again.
        |--------------------------------------------------------------------------
        */

        $interactiveSubSectionIds =
            collect([
                'English',
                'Math',
            ])
                ->map(
                    function ($name) use (
                        $interactive
                    ) {

                        return SubSection::updateOrCreate(
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
                        )->id;
                    }
                );


        /*
        |--------------------------------------------------------------------------
        | English + Math
        |--------------------------------------------------------------------------
        */

        $regularTimetable = [
            'Monday' => [
                '15:45',
                '16:30',
                '17:15',
                '18:00',
            ],

            'Tuesday' => [
                '15:45',
            ],

            'Thursday' => [
                '15:45',
                '16:30',
                '17:15',
                '18:00',
            ],

            'Friday' => [
                '15:45',
            ],
        ];


        foreach ($regularTimetable as $dayName => $times) {

            $day = Day::where(
                'day_name',
                $dayName
            )->firstOrFail();

            foreach ($times as $time) {

                /*
                 * English
                 */
                $this->createOffering(
                    $day->id,
                    $english->id,
                    $time,
                    45,
                    28
                );


                /*
                 * Math
                 *
                 * ONE offering = 41 shared seats.
                 */
                $mathOffering =
                    $this->createOffering(
                        $day->id,
                        $math->id,
                        $time,
                        45,
                        41
                    );


                /*
                 * All Math sub-sections are available
                 * under this one Math offering.
                 */
                $mathSubSectionIds =
                    $math
                        ->subSections()
                        ->where(
                            'is_active',
                            true
                        )
                        ->pluck(
                            'sub_sections.id'
                        );

                $mathOffering
                    ->subSections()
                    ->sync(
                        $mathSubSectionIds
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Interactive
        |--------------------------------------------------------------------------
        */

        $interactiveTimetable = [
            'Monday' => [
                '15:45',
                '16:10',
                '16:30',
                '16:50',
                '17:15',
                '17:35',
                '18:00',
                '18:20',
            ],

            'Tuesday' => [
                '15:45',
                '16:15',
            ],

            'Thursday' => [
                '15:45',
                '16:10',
                '16:30',
                '16:50',
                '17:15',
                '17:35',
                '18:00',
                '18:20',
            ],

            'Friday' => [
                '15:45',
                '16:15',
            ],
        ];


        foreach (
            $interactiveTimetable
            as $dayName => $times
        ) {

            $day = Day::where(
                'day_name',
                $dayName
            )->firstOrFail();

            foreach ($times as $time) {

                $interactiveOffering =
                    $this->createOffering(
                        $day->id,
                        $interactive->id,
                        $time,
                        20,
                        5
                    );


                /*
                 * Interactive is divided into
                 * English and Math sub-sections.
                 */
                $interactiveOffering
                    ->subSections()
                    ->sync(
                        $interactiveSubSectionIds
                    );
            }
        }
    }


    private function createOffering(
        int $dayId,
        int $sectionId,
        string $startTime,
        int $duration,
        int $maxSeats
    ): SectionOffering {

        $start = Carbon::createFromFormat(
            'H:i',
            $startTime
        );

        $end = $start
            ->copy()
            ->addMinutes($duration);


        return SectionOffering::updateOrCreate(
            [
                'day_id' =>
                    $dayId,

                'section_id' =>
                    $sectionId,

                'start_time' =>
                    $start->format('H:i:s'),
            ],
            [
                'duration_minutes' =>
                    $duration,

                'end_time' =>
                    $end->format('H:i:s'),

                'max_seats' =>
                    $maxSeats,

                'is_active' =>
                    true,
            ]
        );
    }
}
