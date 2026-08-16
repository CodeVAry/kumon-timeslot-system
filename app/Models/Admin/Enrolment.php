<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrolment extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id',
        'section_offering_id',
        'enrolment_date',
        'is_wishlist',
        'is_active',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'section_offering_id' => 'integer',
        'enrolment_date' => 'date',
        'is_wishlist' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id',
            'id'
        );
    }

    public function sectionOffering()
    {
        return $this->belongsTo(
            SectionOffering::class,
            'section_offering_id',
            'id'
        );
    }
    public function attendances()
    {
        return $this->hasMany(
            Attendance::class
        );
    }
}
