<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;


    protected $fillable = [
        'external_id',
        'student_status_id',
        'status_changed_at',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'address',
        'can_leave_alone',
        'notes',
        'join_date',
        'is_active',
        'inactive_since',
    ];


    protected $casts = [
        'date_of_birth' =>
            'date',

        'join_date' =>
            'date',

        'inactive_since' =>
            'date',

        'status_changed_at' =>
            'datetime',

        'can_leave_alone' =>
            'boolean',

        'is_active' =>
            'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Automatically Track Status Change Date
    |--------------------------------------------------------------------------
    */

    protected static function booted()
    {
        /*
         * When student is first created,
         * record when the starting status began.
         */
        static::creating(
            function ($student) {

                if (
                    !$student->status_changed_at
                ) {

                    $student->status_changed_at =
                        now();
                }
            }
        );


        /*
         * Whenever student_status_id changes,
         * automatically reset status_changed_at.
         */
        static::updating(
            function ($student) {

                if (
                    $student->isDirty(
                        'student_status_id'
                    )
                ) {

                    $student->status_changed_at =
                        now();
                }
            }
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Student Status
    |--------------------------------------------------------------------------
    */

    public function studentStatus()
    {
        return $this->belongsTo(
            StudentStatus::class,
            'student_status_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Guardians
    |--------------------------------------------------------------------------
    */

    public function guardians()
    {
        return $this->belongsToMany(
            Guardian::class,
            'guardian_student',
            'student_id',
            'guardian_id'
        )
            ->withPivot([
                'relationship',
                'is_primary',
                'is_emergency_contact',
            ])
            ->withTimestamps();
    }


    /*
    |--------------------------------------------------------------------------
    | Enrolments
    |--------------------------------------------------------------------------
    */

    public function enrolments()
    {
        return $this->hasMany(
            Enrolment::class,
            'student_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Leaves
    |--------------------------------------------------------------------------
    */

    public function leaves()
    {
        return $this->hasMany(
            StudentLeave::class
        );
    }
}
