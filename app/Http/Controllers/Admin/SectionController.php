<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Section;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SectionController extends Controller
{
    public function index()
    {
        $sections = Section::orderBy('section_name', 'asc')
            ->paginate(10);

        return view(
            'admin.sections.index',
            compact('sections')
        );
    }

    public function create()
    {
        return view('admin.sections.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'section_name' => trim(
                (string) $request->section_name
            ),
        ]);

        $validated = $request->validate([
            'section_name' => [
                'required',
                'string',
                'max:100',
                'unique:sections,section_name',
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        Section::create([
            'section_name' => $validated['section_name'],

            'description' =>
                $validated['description'] ?? null,

            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.sections.index')
            ->with(
                'success',
                'Section created successfully.'
            );
    }

    public function edit(Section $section)
    {
        return view(
            'admin.sections.edit',
            compact('section')
        );
    }

    public function update(
        Request $request,
        Section $section
    ) {
        $request->merge([
            'section_name' => trim(
                (string) $request->section_name
            ),
        ]);

        $validated = $request->validate([
            'section_name' => [
                'required',
                'string',
                'max:100',
                'unique:sections,section_name,' .
                $section->id,
            ],

            'description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ]);

        $section->update([
            'section_name' => $validated['section_name'],

            'description' =>
                $validated['description'] ?? null,

            'is_active' => $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.sections.index')
            ->with(
                'success',
                'Section updated successfully.'
            );
    }

    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()
            ->route('admin.sections.index')
            ->with(
                'success',
                'Section deleted successfully.'
            );
    }
}
