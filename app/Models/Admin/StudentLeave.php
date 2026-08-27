<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentLeave extends Model
{
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
        'homework_requirement',
        'reason',
        'notes',
        'created_by_user_id',
    ];


    protected $casts = [
        'start_date' =>
            'date',

        'expected_return_date' =>
            'date',

        'actual_return_date' =>
            'date',

        'returned_early' =>
            'boolean',

        'reviewed_at' =>
            'datetime',
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
    | Request Status Scopes
    |--------------------------------------------------------------------------
    */

    public function scopePending($query)
    {
        return $query->where(
            'status',
            'pending'
        );
    }


    public function scopeApproved($query)
    {
        return $query->where(
            'status',
            'approved'
        );
    }


    public function scopeRejected($query)
    {
        return $query->where(
            'status',
            'rejected'
        );
    }


    public function scopeCancelled($query)
    {
        return $query->where(
            'status',
            'cancelled'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Current Approved Leave
    |--------------------------------------------------------------------------
    */

    public function scopeCurrent($query)
    {
        $today =
            now()->toDateString();


        return $query
            ->where(
                'status',
                'approved'
            )
            ->whereDate(
                'start_date',
                '<=',
                $today
            )
            ->whereNull(
                'actual_return_date'
            )
            ->whereDate(
                'expected_return_date',
                '>=',
                $today
            );
    }


    /*
    |--------------------------------------------------------------------------
    | Upcoming Approved Leave
    |--------------------------------------------------------------------------
    */

    public function scopeUpcoming($query)
    {
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
    | Completed Approved Leave
    |--------------------------------------------------------------------------
    */

    public function scopeCompleted($query)
    {
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
