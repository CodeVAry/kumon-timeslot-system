<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentLeave extends Model
{
    /*
    |--------------------------------------------------------------------------
    | Dynamic Vacation / Away Display
    |--------------------------------------------------------------------------
    |
    | Vacation is NOT a permanent StudentStatus.
    |
    | When a student has an active leave record,
    | Vacation temporarily overrides the student's
    | normal status for schedule and export display.
    |
    | The client uses GREY for students who are away.
    |--------------------------------------------------------------------------
    */

    public const VACATION_LABEL = 'Vacation';

    public const VACATION_COLOR = '#9ca3af';


    /*
    |--------------------------------------------------------------------------
    | Fillable
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'student_id',
        'status',
        'requested_by_guardian_id',
        'reviewed_by_user_id',
        'reviewed_at',
        'review_note',
        'start_date',
        'expected_return_date',
        'actual_return_date',
        'returned_early',
        'is_actioned',
        'actioned_at',
        'actioned_by_user_id',
        'homework_requirement',
        'reason',
        'notes',
        'created_by_user_id',
    ];


    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected $casts = [
        'start_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'returned_early' => 'boolean',
        'reviewed_at' => 'datetime',
        'is_actioned' => 'boolean',
        'actioned_at' => 'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(
            Student::class
        );
    }

    public function actionedBy()
    {
        return $this->belongsTo(
            User::class,
            'actioned_by_user_id'
        );
    }


    public function createdBy()
    {
        return $this->belongsTo(
            User::class,
            'created_by_user_id'
        );
    }


    public function requestedByGuardian()
    {
        return $this->belongsTo(
            Guardian::class,
            'requested_by_guardian_id'
        );
    }


    public function reviewedBy()
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by_user_id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Approved / Recorded Leave
    |--------------------------------------------------------------------------
    */

    public function scopeApproved(
        $query
    ) {
        return $query->where(
            'status',
            'approved'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Cancelled Leave
    |--------------------------------------------------------------------------
    */

    public function scopeCancelled(
        $query
    ) {
        return $query->where(
            'status',
            'cancelled'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Leave Active On A Specific Date
    |--------------------------------------------------------------------------
    |
    | A leave is active when:
    |
    | - status is approved
    | - leave has already started
    | - expected return date has not passed
    | - student has not already returned
    |
    | If actual_return_date is the same as the
    | selected date, the student is considered
    | returned on that date and therefore is
    | no longer shown as Vacation.
    |--------------------------------------------------------------------------
    */

    public function scopeActiveOnDate(
        $query,
        $date
    ) {
        $date =
            \Carbon\Carbon::parse(
                $date
            )->toDateString();


        return $query
            ->where(
                'status',
                'approved'
            )
            ->whereDate(
                'start_date',
                '<=',
                $date
            )
            ->whereDate(
                'expected_return_date',
                '>=',
                $date
            )
            ->where(
                function ($query) use ($date) {

                    /*
                     * Student has not returned yet.
                     */
                    $query
                        ->whereNull(
                            'actual_return_date'
                        )

                        /*
                         * Or the student returned
                         * after the date being checked.
                         */
                        ->orWhereDate(
                            'actual_return_date',
                            '>',
                            $date
                        );
                }
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Current Leave
    |--------------------------------------------------------------------------
    */

    public function scopeCurrent(
        $query
    ) {
        return $query->activeOnDate(
            now()->toDateString()
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Upcoming Leave
    |--------------------------------------------------------------------------
    */

    public function scopeUpcoming(
        $query
    ) {
        return $query
            ->where(
                'status',
                'approved'
            )
            ->whereDate(
                'start_date',
                '>',
                now()->toDateString()
            )
            ->whereNull(
                'actual_return_date'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Completed Leave
    |--------------------------------------------------------------------------
    */

    public function scopeCompleted(
        $query
    ) {
        return $query
            ->where(
                'status',
                'approved'
            )
            ->where(
                function ($query) {

                    $query
                        ->whereNotNull(
                            'actual_return_date'
                        )
                        ->orWhereDate(
                            'expected_return_date',
                            '<',
                            now()->toDateString()
                        );
                }
            );
    }
}
