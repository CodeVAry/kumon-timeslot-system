<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\StudentStatus;

class StudentStatusController extends Controller
{
    public function index()
    {
        $studentStatuses = StudentStatus::orderBy(
            'status_name',
            'asc'
        )->paginate(10);

        return view(
            'admin.student-statuses.index',
            compact('studentStatuses')
        );
    }

    public function create()
    {
        return view('admin.student-statuses.create');
    }

    public function store(Request $request)
    {
        $request->merge([
            'status_name' => trim(
                (string) $request->status_name
            ),
        ]);

        $validated = $request->validate([
            'status_name' => [
                'required',
                'string',
                'max:50',
                'unique:student_statuses,status_name',
            ],

            'color_code' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
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

        StudentStatus::create([
            'status_name' =>
                $validated['status_name'],

            'color_code' =>
                $validated['color_code'],

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.student-statuses.index')
            ->with(
                'success',
                'Student status created successfully.'
            );
    }

    public function edit(StudentStatus $studentStatus)
    {
        return view(
            'admin.student-statuses.edit',
            compact('studentStatus')
        );
    }

    public function update(
        Request $request,
        StudentStatus $studentStatus
    ) {
        $request->merge([
            'status_name' => trim(
                (string) $request->status_name
            ),
        ]);

        $validated = $request->validate([
            'status_name' => [
                'required',
                'string',
                'max:50',
                'unique:student_statuses,status_name,' .
                $studentStatus->id,
            ],

            'color_code' => [
                'required',
                'regex:/^#[0-9A-Fa-f]{6}$/',
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

        $studentStatus->update([
            'status_name' =>
                $validated['status_name'],

            'color_code' =>
                $validated['color_code'],

            'description' =>
                $validated['description'] ?? null,

            'is_active' =>
                $validated['is_active'],
        ]);

        return redirect()
            ->route('admin.student-statuses.index')
            ->with(
                'success',
                'Student status updated successfully.'
            );
    }

    public function destroy(StudentStatus $studentStatus)
    {
        $studentStatus->delete();

        return redirect()
            ->route('admin.student-statuses.index')
            ->with(
                'success',
                'Student status deleted successfully.'
            );
    }
}
