<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Day;
use App\Models\Admin\Section;
use App\Models\Admin\SectionOffering;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SectionOfferingController extends Controller
{
    public function index(Request $request)
    {
        /*
         * Active days for the day tabs.
         */
        $days = Day::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        /*
         * Use the first available day when no day is selected.
         */
        $selectedDayId = $request->input(
            'day_id',
            $days->first()?->id
        );

        /*
         * regular = English and Math
         * interactive = Interactive classes
         */
        $viewType = $request->input(
            'view',
            'regular'
        );

        if (
            !in_array(
                $viewType,
                ['regular', 'interactive']
            )
        ) {
            $viewType = 'regular';
        }

        /*
         * Load active sections.
         */
        $sections = Section::where(
            'is_active',
            true
        )
            ->orderBy('section_name')
            ->get();

        $englishSection = $sections->first(
            function ($section) {
                return str_contains(
                    strtolower($section->section_name),
                    'english'
                );
            }
        );

        $interactiveSection = $sections->first(
            function ($section) {
                return str_contains(
                    strtolower($section->section_name),
                    'interactive'
                );
            }
        );

        $mathSections = $sections
            ->filter(function ($section) {
                return str_contains(
                    strtolower($section->section_name),
                    'math'
                );
            })
            ->values();

        /*
         * Load offerings for the selected day.
         *
         * Only active confirmed enrolments occupy seats.
         * Wishlist enrolments do not occupy seats.
         */
        $offerings = SectionOffering::with([
            'day',
            'section',
        ])
            ->withCount([
                'enrolments as allocated_seats' =>
                    function ($query) {
                        $query
                            ->where('is_active', true)
                            ->where('is_wishlist', false);
                    },

                'enrolments as wishlist_count' =>
                    function ($query) {
                        $query
                            ->where('is_active', true)
                            ->where('is_wishlist', true);
                    },
            ])
            ->where('day_id', $selectedDayId)
            ->where('is_active', true)
            ->orderBy('start_time')
            ->get();

        /*
         * English capacity comes from the English offering.
         */
        $englishCapacity = 0;

        if ($englishSection) {
            $englishCapacity = (int) (
                $offerings
                    ->where(
                        'section_id',
                        $englishSection->id
                    )
                    ->max('max_seats')
                ?? 0
            );
        }

        /*
         * Shared Math capacity.
         *
         * This uses the maximum configured max_seats value
         * from the Math offerings as the shared Math limit.
         */
        $mathCapacity = (int) (
            $offerings
                ->whereIn(
                    'section_id',
                    $mathSections->pluck('id')
                )
                ->max('max_seats')
            ?? 0
        );

        /*
         * Group English and Math offerings by starting time.
         */
        $regularOfferings = $offerings->filter(
            function ($offering) use ($interactiveSection) {
                if (!$interactiveSection) {
                    return true;
                }

                return $offering->section_id !==
                    $interactiveSection->id;
            }
        );

        $regularRows = $regularOfferings
            ->groupBy(function ($offering) {
                return Carbon::parse(
                    $offering->start_time
                )->format('H:i:s');
            })
            ->map(function ($timeOfferings, $time) use ($englishSection, $mathSections, $englishCapacity, $mathCapacity) {
                $offeringsBySection =
                    $timeOfferings->keyBy(
                        'section_id'
                    );

                $englishOffering = null;

                if ($englishSection) {
                    $englishOffering =
                        $offeringsBySection->get(
                            $englishSection->id
                        );
                }

                $englishAllocated = (int) (
                    $englishOffering
                            ?->allocated_seats
                    ?? 0
                );

                $englishMaximum = (int) (
                    $englishOffering
                            ?->max_seats
                    ?? $englishCapacity
                );

                $mathAllocations = [];

                foreach ($mathSections as $mathSection) {
                    $mathOffering =
                        $offeringsBySection->get(
                            $mathSection->id
                        );

                    $mathAllocations[
                        $mathSection->id
                    ] = (int) (
                            $mathOffering
                                    ?->allocated_seats
                            ?? 0
                        );
                }

                $mathTotal = array_sum(
                    $mathAllocations
                );

                $englishFull =
                    $englishMaximum > 0 &&
                    $englishAllocated >=
                    $englishMaximum;

                $mathFull =
                    $mathCapacity > 0 &&
                    $mathTotal >=
                    $mathCapacity;

                $englishNearlyFull =
                    $englishMaximum > 0 &&
                    $englishAllocated >=
                    ($englishMaximum * 0.8);

                $mathNearlyFull =
                    $mathCapacity > 0 &&
                    $mathTotal >=
                    ($mathCapacity * 0.8);

                if ($englishFull || $mathFull) {
                    $status = 'Full';

                    $statusClass =
                        'bg-red-100 text-red-600';
                } elseif (
                    $englishNearlyFull ||
                    $mathNearlyFull
                ) {
                    $status = 'Nearly full';

                    $statusClass =
                        'bg-amber-100 text-amber-600';
                } else {
                    $status = 'Available';

                    $statusClass =
                        'bg-green-100 text-green-700';
                }

                return [
                    'time' => Carbon::parse(
                        $time
                    )->format('g:i A'),

                    'sort_time' => $time,

                    'english_allocated' =>
                        $englishAllocated,

                    'english_maximum' =>
                        $englishMaximum,

                    'math_allocations' =>
                        $mathAllocations,

                    'math_total' =>
                        $mathTotal,

                    'math_maximum' =>
                        $mathCapacity,

                    'status' =>
                        $status,

                    'status_class' =>
                        $statusClass,

                    'first_offering_id' =>
                        $timeOfferings->first()?->id,
                ];
            })
            ->sortBy('sort_time')
            ->values();

        /*
         * Group Interactive offerings by starting time.
         */
        $interactiveOfferings = collect();

        if ($interactiveSection) {
            $interactiveOfferings = $offerings
                ->where(
                    'section_id',
                    $interactiveSection->id
                );
        }

        $interactiveRows = $interactiveOfferings
            ->map(function ($offering) {
                $allocated = (int)
                    $offering->allocated_seats;

                $maximum = (int)
                    $offering->max_seats;

                $available = max(
                    0,
                    $maximum - $allocated
                );

                if (
                    $maximum > 0 &&
                    $allocated >= $maximum
                ) {
                    $status = 'Full';

                    $statusClass =
                        'bg-red-100 text-red-600';
                } elseif (
                    $maximum > 0 &&
                    $allocated >=
                    ($maximum * 0.8)
                ) {
                    $status = 'Nearly full';

                    $statusClass =
                        'bg-amber-100 text-amber-600';
                } else {
                    $status = 'Available';

                    $statusClass =
                        'bg-green-100 text-green-700';
                }

                return [
                    'id' =>
                        $offering->id,

                    'time' =>
                        Carbon::parse(
                            $offering->start_time
                        )->format('g:i A'),

                    'end_time' =>
                        Carbon::parse(
                            $offering->end_time
                        )->format('g:i A'),

                    'allocated' =>
                        $allocated,

                    'maximum' =>
                        $maximum,

                    'available' =>
                        $available,

                    'wishlist_count' =>
                        (int)
                        $offering->wishlist_count,

                    'status' =>
                        $status,

                    'status_class' =>
                        $statusClass,
                ];
            })
            ->values();

        $selectedDay = $days->firstWhere(
            'id',
            (int) $selectedDayId
        );

        return view(
            'admin.section-offerings.index',
            compact(
                'days',
                'selectedDayId',
                'selectedDay',
                'viewType',
                'englishSection',
                'interactiveSection',
                'mathSections',
                'englishCapacity',
                'mathCapacity',
                'regularRows',
                'interactiveRows'
            )
        );
    }

    public function create()
    {
        $days = Day::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        $sections = Section::where('is_active', true)
            ->orderBy('section_name', 'asc')
            ->get();

        return view(
            'admin.section-offerings.create',
            compact('days', 'sections')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'day_ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:days,id',
            ],

            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'duration_minutes' => [
                'required',
                'integer',
                'min:5',
                'max:480',
            ],

            'max_seats' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $section = Section::where(
            'id',
            $validated['section_id']
        )
            ->where('is_active', true)
            ->first();

        if (!$section) {
            return back()
                ->withInput()
                ->withErrors([
                    'section_id' =>
                        'The selected section is unavailable.',
                ]);
        }

        $selectedDays = Day::whereIn(
            'id',
            $validated['day_ids']
        )
            ->where('is_active', true)
            ->get();

        if (
            $selectedDays->count() !==
            count($validated['day_ids'])
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'day_ids' =>
                        'One or more selected days are unavailable.',
                ]);
        }

        $startTime = Carbon::createFromFormat(
            'H:i',
            $validated['start_time']
        );

        $endTime = $startTime
            ->copy()
            ->addMinutes(
                $validated['duration_minutes']
            );

        if (
            $startTime->format('Y-m-d') !==
            $endTime->format('Y-m-d')
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'duration_minutes' =>
                        'The class must finish on the same day.',
                ]);
        }

        $startTimeValue = $startTime->format('H:i:s');
        $endTimeValue = $endTime->format('H:i:s');

        /*
         * Prevent overlapping offerings for the same
         * section on the same day.
         */
        foreach ($selectedDays as $day) {
            $overlapExists = SectionOffering::where(
                'day_id',
                $day->id
            )
                ->where(
                    'section_id',
                    $validated['section_id']
                )
                ->where(
                    'start_time',
                    '<',
                    $endTimeValue
                )
                ->where(
                    'end_time',
                    '>',
                    $startTimeValue
                )
                ->exists();

            if ($overlapExists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'start_time' =>
                            $section->section_name .
                            ' already has an overlapping offering on ' .
                            $day->day_name .
                            '.',
                    ]);
            }
        }

        DB::transaction(function () use ($selectedDays, $validated, $startTimeValue, $endTimeValue) {
            foreach ($selectedDays as $day) {
                SectionOffering::create([
                    'day_id' => $day->id,

                    'section_id' =>
                        $validated['section_id'],

                    'start_time' =>
                        $startTimeValue,

                    'duration_minutes' =>
                        $validated['duration_minutes'],

                    'end_time' =>
                        $endTimeValue,

                    'max_seats' =>
                        $validated['max_seats'],

                    'is_active' =>
                        $validated['is_active'],
                ]);
            }
        });

        return redirect()
            ->route('admin.section-offerings.index')
            ->with(
                'success',
                $selectedDays->count() .
                ' section offering(s) created successfully.'
            );
    }

    public function edit(
        SectionOffering $sectionOffering
    ) {
        $days = Day::where('is_active', true)
            ->orWhere(
                'id',
                $sectionOffering->day_id
            )
            ->orderBy('sort_order', 'asc')
            ->get();

        $sections = Section::where('is_active', true)
            ->orWhere(
                'id',
                $sectionOffering->section_id
            )
            ->orderBy('section_name', 'asc')
            ->get();

        return view(
            'admin.section-offerings.edit',
            compact(
                'sectionOffering',
                'days',
                'sections'
            )
        );
    }

    public function update(
        Request $request,
        SectionOffering $sectionOffering
    ) {
        $validated = $request->validate([
            'day_id' => [
                'required',
                'integer',
                'exists:days,id',
            ],

            'section_id' => [
                'required',
                'integer',
                'exists:sections,id',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'duration_minutes' => [
                'required',
                'integer',
                'min:5',
                'max:480',
            ],

            'max_seats' => [
                'required',
                'integer',
                'min:1',
                'max:1000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $day = Day::find($validated['day_id']);

        $section = Section::find(
            $validated['section_id']
        );

        if (!$day || !$section) {
            return back()
                ->withInput()
                ->withErrors([
                    'day_id' =>
                        'The selected day or section is unavailable.',
                ]);
        }

        $startTime = Carbon::createFromFormat(
            'H:i',
            $validated['start_time']
        );

        $endTime = $startTime
            ->copy()
            ->addMinutes(
                $validated['duration_minutes']
            );

        if (
            $startTime->format('Y-m-d') !==
            $endTime->format('Y-m-d')
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'duration_minutes' =>
                        'The class must finish on the same day.',
                ]);
        }

        $startTimeValue = $startTime->format('H:i:s');
        $endTimeValue = $endTime->format('H:i:s');

        $overlapExists = SectionOffering::where(
            'day_id',
            $validated['day_id']
        )
            ->where(
                'section_id',
                $validated['section_id']
            )
            ->where(
                'id',
                '!=',
                $sectionOffering->id
            )
            ->where(
                'start_time',
                '<',
                $endTimeValue
            )
            ->where(
                'end_time',
                '>',
                $startTimeValue
            )
            ->exists();

        if ($overlapExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'start_time' =>
                        $section->section_name .
                        ' already has an overlapping offering on ' .
                        $day->day_name .
                        '.',
                ]);
        }

        $sectionOffering->update([
            'day_id' =>
                $validated['day_id'],

            'section_id' =>
                $validated['section_id'],

            'start_time' =>
                $startTimeValue,

            'duration_minutes' =>
                $validated['duration_minutes'],

            'end_time' =>
                $endTimeValue,

            'max_seats' =>
                $validated['max_seats'],

            'is_active' =>
                $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.section-offerings.index')
            ->with(
                'success',
                'Section offering updated successfully.'
            );
    }

    public function destroy(
        SectionOffering $sectionOffering
    ) {
        $sectionOffering->delete();

        return redirect()
            ->route('admin.section-offerings.index')
            ->with(
                'success',
                'Section offering deleted successfully.'
            );
    }

}
