<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\StudentImportAlias;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StudentImportAliasController extends Controller
{
    public function store(
        Request $request
    ): RedirectResponse {

        $validated =
            $request->validate([
                'source_name' => [
                    'required',
                    'string',
                    'max:255',
                ],

                'target_external_id' => [
                    'required',
                    'string',
                    'max:100',
                ],
            ]);


        $sourceName =
            $this->normalizeName(
                $validated[
                    'source_name'
                ]
            );


        StudentImportAlias::updateOrCreate(
            [
                'source_name' =>
                    $sourceName,
            ],
            [
                'target_external_id' =>
                    trim(
                        $validated[
                            'target_external_id'
                        ]
                    ),
            ]
        );


        session()->forget([
            'student_import.preview',
            'student_import.preview_rows',
            'student_import.ready_rows',
            'student_import.problem_rows',
        ]);


        return redirect()
            ->route(
                'admin.student-import.preview'
            )
            ->with(
                'success',
                'Student name mapping saved.'
            );
    }


    private function normalizeName(
        string $name
    ): string {

        $name =
            mb_strtolower(
                trim(
                    $name
                )
            );


        $name =
            preg_replace(
                '/[^a-z0-9]+/u',
                ' ',
                $name
            );


        $name =
            preg_replace(
                '/\s+/',
                ' ',
                $name
            );


        return trim(
            $name
        );
    }
}
