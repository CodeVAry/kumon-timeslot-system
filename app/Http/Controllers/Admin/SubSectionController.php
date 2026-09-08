<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Section;
use App\Models\Admin\SubSection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubSectionController extends Controller
{
    public function index()
    {
        $subSections = SubSection::with('section')
            ->orderBy('sub_section_name')
            ->paginate(20);

        return view(
            'admin.sub-sections.index',
            compact('subSections')
        );
    }


    public function create()
    {
        $sections = Section::where(
            'is_active',
            true
        )
            ->orderBy('section_name')
            ->get();

        return view(
            'admin.sub-sections.create',
            compact('sections')
        );
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'sub_section_name' => [
                'required',
                'string',
                'max:100',

                Rule::unique('sub_sections')
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'section_id',
                                $request->section_id
                            )
                    ),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] =
            $request->boolean('is_active');

        SubSection::create($validated);

        return redirect()
            ->route(
                'admin.sub-sections.index'
            )
            ->with(
                'success',
                'Sub-section created successfully.'
            );
    }


    public function edit(
        SubSection $subSection
    ) {
        $sections = Section::where(
            'is_active',
            true
        )
            ->orWhere(
                'id',
                $subSection->section_id
            )
            ->orderBy('section_name')
            ->get();

        return view(
            'admin.sub-sections.edit',
            compact(
                'subSection',
                'sections'
            )
        );
    }


    public function update(
        Request $request,
        SubSection $subSection
    ) {
        $validated = $request->validate([
            'section_id' => [
                'required',
                'exists:sections,id',
            ],

            'sub_section_name' => [
                'required',
                'string',
                'max:100',

                Rule::unique('sub_sections')
                    ->where(
                        fn ($query) =>
                            $query->where(
                                'section_id',
                                $request->section_id
                            )
                    )
                    ->ignore(
                        $subSection->id
                    ),
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $validated['is_active'] =
            $request->boolean('is_active');

        $subSection->update($validated);

        return redirect()
            ->route(
                'admin.sub-sections.index'
            )
            ->with(
                'success',
                'Sub-section updated successfully.'
            );
    }


    public function destroy(
        SubSection $subSection
    ) {
        if (
            $subSection
                ->enrolments()
                ->exists()
        ) {
            return back()->with(
                'error',
                'This sub-section cannot be deleted because it is already used by student enrolments.'
            );
        }

        $subSection->delete();

        return redirect()
            ->route(
                'admin.sub-sections.index'
            )
            ->with(
                'success',
                'Sub-section deleted successfully.'
            );
    }
}
