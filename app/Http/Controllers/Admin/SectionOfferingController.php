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
     public function index()
    {
        $sectionOfferings = SectionOffering::with([
            'day',
            'section',
        ])
            ->select('section_offerings.*')
            ->join(
                'days',
                'days.id',
                '=',
                'section_offerings.day_id'
            )
            ->join(
                'sections',
                'sections.id',
                '=',
                'section_offerings.section_id'
            )
            ->orderBy('days.sort_order', 'asc')
            ->orderBy('section_offerings.start_time', 'asc')
            ->orderBy('sections.section_name', 'asc')
            ->paginate(15);

        return view(
            'admin.section-offerings.index',
            compact('sectionOfferings')
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

        DB::transaction(function () use (
            $selectedDays,
            $validated,
            $startTimeValue,
            $endTimeValue
        ) {
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
