<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'enrolment_id',
        'attendance_date',
        'status',
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    public function enrolment()
    {
        return $this->belongsTo(
            Enrolment::class
        );
    }
}
