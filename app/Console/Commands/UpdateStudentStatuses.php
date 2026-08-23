<?php

namespace App\Console\Commands;

use App\Models\Admin\Student;
use App\Models\Admin\StudentStatus;
use Illuminate\Console\Command;

class UpdateStudentStatuses extends Command
{
    /**
     * Command name.
     */
    protected $signature =
        'students:update-statuses';


    /**
     * Command description.
     */
    protected $description =
        'Automatically update student statuses based on business rules.';


    /**
     * Execute command.
     */
    public function handle()
    {
        /*
        |--------------------------------------------------------------------------
        | Find New Status
        |--------------------------------------------------------------------------
        */

        $newStatus =
            StudentStatus::whereRaw(
                'LOWER(status_name) = ?',
                [
                    'new',
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        /*
        |--------------------------------------------------------------------------
        | Find Active Status
        |--------------------------------------------------------------------------
        */

        $activeStatus =
            StudentStatus::whereRaw(
                'LOWER(status_name) = ?',
                [
                    'active',
                ]
            )
                ->where(
                    'is_active',
                    true
                )
                ->first();


        if (
            !$newStatus
            ||
            !$activeStatus
        ) {

            $this->error(
                'New or Active student status could not be found.'
            );


            return Command::FAILURE;
        }


        /*
        |--------------------------------------------------------------------------
        | New -> Active After 15 Days
        |--------------------------------------------------------------------------
        */

        $students =
            Student::where(
                'student_status_id',
                $newStatus->id
            )
                ->where(
                    'is_active',
                    true
                )
                ->get();


        $updatedCount =
            0;


        foreach (
            $students
            as $student
        ) {

            /*
             * For old records that existed
             * before status_changed_at was added,
             * fall back to join_date / created_at.
             */
            $statusStartedAt =
                $student
                    ->status_changed_at
                ??
                $student
                    ->join_date
                ??
                $student
                    ->created_at;


            if (!$statusStartedAt) {

                continue;
            }


            /*
             * Must have been New
             * for at least 15 days.
             */
            if (
                $statusStartedAt->copy()
                    ->addDays(15)
                    ->isFuture()
            ) {

                continue;
            }


            $student->update([
                'student_status_id' =>
                    $activeStatus->id,

                'is_active' =>
                    true,

                'inactive_since' =>
                    null,
            ]);


            $updatedCount++;
        }


        $this->info(
            $updatedCount
            . ' student(s) changed from New to Active.'
        );


        return Command::SUCCESS;
    }
}
