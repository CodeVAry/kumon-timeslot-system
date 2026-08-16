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
        'date_of_birth' => 'date',
        'join_date' => 'date',
        'inactive_since' => 'date',
        'can_leave_alone' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function studentStatus()
    {
        return $this->belongsTo(
            StudentStatus::class,
            'student_status_id',
            'id'
        );
    }

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

    public function enrolments()
    {
        return $this->hasMany(
            Enrolment::class,
            'student_id',
            'id'
        );
    }
    public function leaves()
    {
        return $this->hasMany(
            StudentLeave::class
        );
    }


}
