<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Admin\Enrolment;

use App\Models\Admin\Student;

use App\Models\Admin\StudentImportAlias;

use App\Services\StudentProfileImportService;

use App\Services\StudentTimeslotImportService;

use Illuminate\Http\RedirectResponse;

use Illuminate\Http\Request;

use Illuminate\Support\Collection;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Storage;

class StudentImportController extends Controller

{

    public function __construct(

        protected StudentProfileImportService $profileService,

        protected StudentTimeslotImportService $timeslotService

    ) {

    }

    public function index()

    {

        return view('admin.students.import.index', [

            'profileUploaded' => session()->has('student_import.profile_path'),

            'timeslotUploaded' => session()->has('student_import.timeslot_path'),

            'profileName' => session('student_import.profile_name'),

            'timeslotName' => session('student_import.timeslot_name'),

        ]);

    }

    public function uploadProfile(Request $request): RedirectResponse

    {

        $validated = $request->validate([

            'profile_file' => [

                'required',

                'file',

                'mimes:xlsx,xls,csv',

                'max:10240',

            ],

        ]);

        $oldPath = session('student_import.profile_path');

        if ($oldPath && Storage::disk('local')->exists($oldPath)) {

            Storage::disk('local')->delete($oldPath);

        }

        $file = $validated['profile_file'];

        $path = $file->store(

            'student-imports',

            'local'

        );

        session([

            'student_import.profile_path' => $path,

            'student_import.profile_name' => $file->getClientOriginalName(),

        ]);

        $this->clearPreviewSession();

        return redirect()

            ->route('admin.student-import.index')

            ->with(

                'success',

                'Student Profile Report uploaded successfully.'

            );

    }

    public function uploadTimeslot(Request $request): RedirectResponse

    {

        $validated = $request->validate([

            'timeslot_file' => [

                'required',

                'file',

                'mimes:xlsx,xls,csv',

                'max:10240',

            ],

        ]);

        $oldPath = session('student_import.timeslot_path');

        if ($oldPath && Storage::disk('local')->exists($oldPath)) {

            Storage::disk('local')->delete($oldPath);

        }

        $file = $validated['timeslot_file'];

        $path = $file->store(

            'student-imports',

            'local'

        );

        session([

            'student_import.timeslot_path' => $path,

            'student_import.timeslot_name' => $file->getClientOriginalName(),

        ]);

        $this->clearPreviewSession();

        return redirect()

            ->route('admin.student-import.index')

            ->with(

                'success',

                'Centre Timeslots workbook uploaded successfully.'

            );

    }

    public function preview()

    {

        $paths = $this->getImportPaths();

        if ($paths instanceof RedirectResponse) {

            return $paths;

        }

        try {

            $profileData = $this->profileService->parse(

                $paths['profile_full_path']

            );

            $profileIndex = $this->profileService->indexByName(

                $profileData

            );

            $profileCandidates = collect($profileData)

                ->filter(function ($profile) {

                    return !empty($profile['external_id'])

                        && (

                            !empty($profile['first_name'])

                            || !empty($profile['last_name'])

                        );

                })

                ->sortBy(function ($profile) {

                    return mb_strtolower(

                        trim(

                            ($profile['first_name'] ?? '')

                            . ' '

                            . ($profile['last_name'] ?? '')

                        )

                    );

                })

                ->values();

            $timeslotRows = $this->timeslotService->parse(

                $paths['timeslot_full_path']

            );

            $matchedRows = $this->timeslotService->matchWithProfiles(

                $timeslotRows,

                $profileIndex

            );

            /*

             * IMPORTANT:

             * Check the database after spreadsheet matching.

             *

             * This prevents a second import from showing all rows as new.

             */

            $matchedRows = $this->classifyImportRows(

                $matchedRows

            );

            $matchedCollection = collect($matchedRows);

            $problemRows = $matchedCollection

                ->where('import_state', 'problem')

                ->values();

            $alreadyImportedRows = $matchedCollection

                ->where('import_state', 'already_imported')

                ->values();

            $newStudentRows = $matchedCollection

                ->where('import_state', 'new_student')

                ->values();

            $newAllocationRows = $matchedCollection

                ->filter(function ($row) {

                    return in_array(

                        $row['import_state'] ?? null,

                        [

                            'new_allocation',

                            'reactivate_allocation',

                        ],

                        true

                    );

                })

                ->values();

            /*

             * Only these rows should be processed by Confirm Import.

             */

            $readyRows = $matchedCollection

                ->filter(function ($row) {

                    return in_array(

                        $row['import_state'] ?? null,

                        [

                            'new_student',

                            'new_allocation',

                            'reactivate_allocation',

                        ],

                        true

                    );

                })

                ->values();

            /*

             * Unique students that do not exist in students table yet.

             */

            $studentsNotAdded = $newStudentRows

                ->unique(function ($row) {

                    return $row['profile']['external_id']

                        ?? $row['student_name']

                        ?? uniqid();

                })

                ->values();

            /*

             * Unique students already in students table.

             */

            $existingStudents = $matchedCollection

                ->filter(function ($row) {

                    return in_array(

                        $row['import_state'] ?? null,

                        [

                            'already_imported',

                            'new_allocation',

                            'reactivate_allocation',

                        ],

                        true

                    );

                })

                ->unique(function ($row) {

                    return $row['profile']['external_id']

                        ?? $row['existing_student_id']

                        ?? $row['student_name']

                        ?? uniqid();

                })

                ->values();

            $alreadyImportedStudents = $alreadyImportedRows

                ->unique(function ($row) {

                    return $row['profile']['external_id']

                        ?? $row['existing_student_id']

                        ?? $row['student_name']

                        ?? uniqid();

                })

                ->values();

            /*

             * Unique recognised students in the uploaded timeslot.

             */

            $currentStudentCount = $matchedCollection

                ->filter(function ($row) {

                    return !empty(

                        $row['profile']['external_id']

                        ?? null

                    );

                })

                ->unique(function ($row) {

                    return $row['profile']['external_id'];

                })

                ->count();

            session([

                'student_import.preview_rows' => $matchedRows,

            ]);

            return view('admin.students.import.preview', [

                'matchedRows' => $matchedCollection,

                'readyRows' => $readyRows,

                'problemRows' => $problemRows,

                'alreadyImportedRows' => $alreadyImportedRows,

                'newStudentRows' => $newStudentRows,

                'newAllocationRows' => $newAllocationRows,

                'studentsNotAdded' => $studentsNotAdded,

                'existingStudents' => $existingStudents,

                'alreadyImportedStudents' => $alreadyImportedStudents,

                'currentStudentCount' => $currentStudentCount,

                'profileCandidates' => $profileCandidates,

            ]);

        } catch (\Throwable $e) {

            report($e);

            return redirect()

                ->route('admin.student-import.index')

                ->with(

                    'error',

                    'Unable to preview import: ' . $e->getMessage()

                );

        }

    }

    public function confirm(Request $request): RedirectResponse

    {

        $paths = $this->getImportPaths();

        if ($paths instanceof RedirectResponse) {

            return $paths;

        }

        $submitted = $request->validate([
            'matches' => ['sometimes', 'array', 'max:2000'],
            'matches.*.selected' => ['sometimes', 'in:1'],
            'matches.*.source_name' => ['nullable', 'string', 'max:255'],
            'matches.*.target_external_id' => ['nullable', 'string', 'max:100'],
        ])['matches'] ?? [];

        try {

            /*

             * Always rebuild everything before writing.

             * Do not trust an old preview stored in the session.

             */

            $profileData = $this->profileService->parse(

                $paths['profile_full_path']

            );

            $profileIndex = $this->profileService->indexByName(

                $profileData

            );

            $timeslotRows = $this->timeslotService->parse(

                $paths['timeslot_full_path']

            );

            $selectedMappings = [];
            if ($submitted) {
                // Match row indices against the freshly parsed files, never against
                // an old session preview or an unverified source name in the POST.
                $unmatched = collect($this->classifyImportRows(
                    $this->timeslotService->matchWithProfiles(
                        $timeslotRows,
                        $profileIndex
                    )
                ))->where('import_state', 'problem')->values();

                $validIds = collect($profileData)
                    ->pluck('external_id')
                    ->filter()
                    ->map(fn ($id) => trim((string) $id))
                    ->all();

                foreach ($submitted as $index => $match) {
                    if (empty($match['selected'])) {
                        continue;
                    }

                    $row = $unmatched->get($index);
                    $sourceName = (string) ($row['student_name'] ?? '');
                    $source = $this->normalizeImportName($sourceName);
                    $target = trim((string) ($match['target_external_id'] ?? ''));

                    if (!$row || !in_array(
                        $row['status'] ?? null,
                        ['student_not_found', 'student_ambiguous'],
                        true
                    ) || !$source || $sourceName !== ($match['source_name'] ?? null)) {
                        throw new \RuntimeException('An unmatched row has changed. Refresh the preview and try again.');
                    }
                    if (!$target || !in_array($target, $validIds, true)) {
                        throw new \RuntimeException('Select a valid student profile for ' . $sourceName . '.');
                    }
                    if (isset($selectedMappings[$source]) && $selectedMappings[$source] !== $target) {
                        throw new \RuntimeException('Choose the same profile for every occurrence of ' . $sourceName . '.');
                    }
                    $selectedMappings[$source] = $target;
                }

                DB::transaction(function () use ($selectedMappings) {
                    foreach ($selectedMappings as $source => $target) {
                        StudentImportAlias::updateOrCreate(
                            ['source_name' => $source],
                            ['target_external_id' => $target]
                        );
                    }
                });
            }

            $matchedRows = $this->timeslotService->matchWithProfiles(

                $timeslotRows,

                $profileIndex

            );

            // Apply only the checked, validated choices to this import. This
            // also works when an older matching service has not picked up the
            // newly saved aliases yet.
            if ($selectedMappings) {
                $chosenProfiles = collect($profileData)->filter(
                    fn ($profile) => !empty($profile['external_id'])
                )->keyBy(fn ($profile) => trim((string) $profile['external_id']));

                foreach ($matchedRows as &$row) {
                    $source = $this->normalizeImportName($row['student_name'] ?? '');
                    if (!isset($selectedMappings[$source])
                        || !in_array($row['status'] ?? null, [
                            'student_not_found', 'student_ambiguous', 'ready',
                        ], true)
                        || empty($row['offering_id'])) {
                        continue;
                    }
                    $row['profile'] = $chosenProfiles->get($selectedMappings[$source]);
                    $row['status'] = 'ready';
                    $row['message'] = 'Matched by admin during import.';
                }
                unset($row);

                // A selected name can occur more than once in the workbook.
                $seen = [];
                foreach ($matchedRows as &$row) {
                    if (($row['status'] ?? null) !== 'ready' || empty($row['profile']['external_id'])) {
                        continue;
                    }
                    $key = $row['profile']['external_id'] . '|'
                        . ($row['offering_id'] ?? '') . '|'
                        . ($row['sub_section_id'] ?? '');
                    if (isset($seen[$key])) {
                        $row['status'] = 'duplicate';
                        $row['message'] = 'Duplicate allocation in workbook - skipped.';
                    } else {
                        $seen[$key] = true;
                    }
                }
                unset($row);
            }

            foreach ($selectedMappings as $source => $target) {
                $resolved = collect($matchedRows)->contains(function ($row) use ($source, $target) {
                    return $this->normalizeImportName($row['student_name'] ?? '') === $source
                        && (string) ($row['profile']['external_id'] ?? '') === $target;
                });
                if (!$resolved) {
                    throw new \RuntimeException(
                        'The mapping for ' . $source . ' did not resolve. Check the timeslot matching service.'
                    );
                }
            }

            $matchedRows = $this->classifyImportRows(

                $matchedRows

            );

            /*

             * Do not process rows that are already fully imported.

             */

            $readyRows = collect($matchedRows)

                ->filter(function ($row) {

                    return in_array(

                        $row['import_state'] ?? null,

                        [

                            'new_student',

                            'new_allocation',

                            'reactivate_allocation',

                        ],

                        true

                    );

                })

                ->values();

            $alreadyImportedCount = collect($matchedRows)

                ->where('import_state', 'already_imported')

                ->count();

            if ($readyRows->isEmpty()) {

                return redirect()

                    ->route('admin.student-import.preview')

                    ->with(

                        'success',

                        'Nothing new to import. '

                        . $alreadyImportedCount

                        . ' allocation(s) are already in the system.'

                    );

            }

            $profilesByExternalId = collect($profileData)

                ->filter(function ($profile) {

                    return !empty($profile['external_id']);

                })

                ->keyBy('external_id');

            $processedStudents = [];

            $newStudentCount = 0;

            $existingStudentCount = 0;

            $newAllocationCount = 0;

            $reactivatedAllocationCount = 0;

            DB::transaction(

                function () use (

                    $readyRows,

                    $profilesByExternalId,

                    &$processedStudents,

                    &$newStudentCount,

                    &$existingStudentCount,

                    &$newAllocationCount,

                    &$reactivatedAllocationCount

                ) {

                    foreach ($readyRows as $row) {

                        $profile = $row['profile'] ?? null;

                        if (!$profile) {

                            continue;

                        }

                        $externalId = $profile['external_id'] ?? null;

                        if (!$externalId) {

                            throw new \RuntimeException(

                                'Student ID is missing for '

                                . ($row['student_name'] ?? 'Unknown Student')

                            );

                        }

                        /*

                         * Resolve/create the student only once per Student ID.

                         */

                        if (isset($processedStudents[$externalId])) {

                            $student = $processedStudents[$externalId];

                        } else {

                            $student = Student::where(

                                'external_id',

                                $externalId

                            )->first();

                            $wasExistingStudent = (bool) $student;

                            $fullProfile = $profilesByExternalId

                                ->get($externalId);

                            /*

                             * If this is a normal profile match, create/update

                             * student and guardians from the profile report.

                             */

                            if ($fullProfile) {

                                $student = $this->profileService

                                    ->importProfileRow(

                                        $fullProfile

                                    );

                            }

                            /*

                             * Saved alias could point to a student that is

                             * already in the database.

                             */

                            if (

                                !$student

                                && !empty($profile['id'])

                            ) {

                                $student = Student::find(

                                    $profile['id']

                                );

                            }

                            if (!$student) {

                                throw new \RuntimeException(

                                    'Unable to resolve student: '

                                    . ($row['student_name'] ?? 'Unknown Student')

                                );

                            }

                            $processedStudents[$externalId] = $student;

                            if ($wasExistingStudent) {

                                $existingStudentCount++;

                            } else {

                                $newStudentCount++;

                            }

                        }

                        $state = $row['import_state'] ?? null;

                        /*

                         * importAllocation() is still the final duplicate guard.

                         */

                        $this->timeslotService->importAllocation(

                            $student,

                            $row

                        );

                        if ($state === 'reactivate_allocation') {

                            $reactivatedAllocationCount++;

                        } else {

                            $newAllocationCount++;

                        }

                    }

                }

            );

            $this->clearPreviewSession();

            return redirect()

                ->route('admin.students.index')

                ->with(

                    'success',

                    $newStudentCount

                    . ' new student(s) added, '

                    . $existingStudentCount

                    . ' existing student(s) updated/reused, '

                    . $newAllocationCount

                    . ' new allocation(s) added, '

                    . $reactivatedAllocationCount

                    . ' allocation(s) reactivated. '

                    . $alreadyImportedCount

                    . ' existing allocation(s) skipped.'

                );

        } catch (\Throwable $e) {

            report($e);

            return redirect()

                ->route('admin.student-import.preview')

                ->with(

                    'error',

                    'Import failed: ' . $e->getMessage()

                );

        }

    }

    public function reset(): RedirectResponse

    {

        $profilePath = session(

            'student_import.profile_path'

        );

        $timeslotPath = session(

            'student_import.timeslot_path'

        );

        if (

            $profilePath

            && Storage::disk('local')->exists($profilePath)

        ) {

            Storage::disk('local')->delete($profilePath);

        }

        if (

            $timeslotPath

            && Storage::disk('local')->exists($timeslotPath)

        ) {

            Storage::disk('local')->delete($timeslotPath);

        }

        session()->forget([

            'student_import.profile_path',

            'student_import.profile_name',

            'student_import.timeslot_path',

            'student_import.timeslot_name',

            'student_import.preview',

            'student_import.preview_rows',

            'student_import.ready_rows',

            'student_import.problem_rows',

        ]);

        return redirect()

            ->route('admin.student-import.index')

            ->with(

                'success',

                'Import files have been cleared.'

            );

    }

    /*

    |--------------------------------------------------------------------------

    | Classify matched rows against the existing database

    |--------------------------------------------------------------------------

    |

    | new_student:

    |   Student ID is not yet in students table.

    |

    | new_allocation:

    |   Student exists, but this class/day/time does not.

    |

    | reactivate_allocation:

    |   Same enrolment exists but is inactive.

    |

    | already_imported:

    |   Student and active enrolment already exist.

    |

    | problem:

    |   Spreadsheet matching itself failed.

    |--------------------------------------------------------------------------

    */

    private function classifyImportRows(

        array $matchedRows

    ): array {

        $classified = [];

        foreach ($matchedRows as $row) {

            if (

                ($row['status'] ?? null)

                !== 'ready'

            ) {

                $row['import_state'] = 'problem';

                $classified[] = $row;

                continue;

            }

            $externalId =

                $row['profile']['external_id']

                ?? null;

            if (!$externalId) {

                $row['status'] = 'student_not_found';

                $row['import_state'] = 'problem';

                $row['message'] =

                    'Student ID is missing from the matched profile.';

                $classified[] = $row;

                continue;

            }

            $student = Student::where(

                'external_id',

                $externalId

            )->first();

            /*

             * Student has never been imported.

             */

            if (!$student) {

                $row['import_state'] = 'new_student';

                $row['message'] =

                    'New student - not yet added to the system.';

                $classified[] = $row;

                continue;

            }

            $row['existing_student_id'] = $student->id;

            $query = Enrolment::where(

                'student_id',

                $student->id

            )

                ->where(

                    'section_offering_id',

                    $row['offering_id']

                )

                ->where(

                    'is_wishlist',

                    false

                );

            if (!empty($row['sub_section_id'])) {

                $query->where(

                    'sub_section_id',

                    $row['sub_section_id']

                );

            } else {

                $query->whereNull(

                    'sub_section_id'

                );

            }

            $existingEnrolment = $query->first();

            /*

             * Student exists but this allocation is new.

             */

            if (!$existingEnrolment) {

                $row['import_state'] = 'new_allocation';

                $row['message'] =

                    'Student already exists - this is a new class allocation.';

                $classified[] = $row;

                continue;

            }

            /*

             * Same allocation exists but had previously been disabled.

             */

            if (!$existingEnrolment->is_active) {

                $row['import_state'] =

                    'reactivate_allocation';

                $row['message'] =

                    'Student already exists - this inactive allocation will be reactivated.';

                $classified[] = $row;

                continue;

            }

            /*

             * Nothing needs to be imported.

             */

            $row['import_state'] =

                'already_imported';

            $row['message'] =

                'Already imported - student and class allocation already exist.';

            $classified[] = $row;

        }

        return $classified;

    }

    private function normalizeImportName(string $name): string
    {
        $name = mb_strtolower(trim($name));
        $name = preg_replace('/[^a-z0-9]+/u', ' ', $name);
        return trim(preg_replace('/\s+/u', ' ', $name));
    }

    private function getImportPaths():

        array|RedirectResponse

    {

        $profilePath = session(

            'student_import.profile_path'

        );

        $timeslotPath = session(

            'student_import.timeslot_path'

        );

        if (!$profilePath || !$timeslotPath) {

            return redirect()

                ->route('admin.student-import.index')

                ->with(

                    'error',

                    'Please upload both files before continuing.'

                );

        }

        if (

            !Storage::disk('local')->exists($profilePath)

            || !Storage::disk('local')->exists($timeslotPath)

        ) {

            return redirect()

                ->route('admin.student-import.index')

                ->with(

                    'error',

                    'One or both uploaded files are no longer available. Please upload them again.'

                );

        }

        return [

            'profile_path' => $profilePath,

            'timeslot_path' => $timeslotPath,

            'profile_full_path' =>

                Storage::disk('local')->path(

                    $profilePath

                ),

            'timeslot_full_path' =>

                Storage::disk('local')->path(

                    $timeslotPath

                ),

        ];

    }

    private function clearPreviewSession(): void

    {

        session()->forget([

            'student_import.preview',

            'student_import.preview_rows',

            'student_import.ready_rows',

            'student_import.problem_rows',

        ]);

    }

}
