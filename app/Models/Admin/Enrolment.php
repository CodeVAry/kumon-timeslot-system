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
        'wishlist_for_enrolment_id',
        'enrolment_date',
        'is_wishlist',
        'is_active',
    ];

    protected $casts = [
        'student_id' => 'integer',
        'section_offering_id' => 'integer',
        'wishlist_for_enrolment_id' => 'integer',
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
            Attendance::class,
            'enrolment_id',
            'id'
        );
    }

    public function wishlistForEnrolment()
    {
        return $this->belongsTo(
            Enrolment::class,
            'wishlist_for_enrolment_id',
            'id'
        );
    }

    public function wishlistRequests()
    {
        return $this->hasMany(
            Enrolment::class,
            'wishlist_for_enrolment_id',
            'id'
        );
    }

    public function scopeActive($query)
    {
        return $query->where(
            'is_active',
            true
        );
    }

    public function scopeConfirmed($query)
    {
        return $query
            ->where(
                'is_active',
                true
            )
            ->where(
                'is_wishlist',
                false
            );
    }

    public function scopeWishlist($query)
    {
        return $query
            ->where(
                'is_active',
                true
            )
            ->where(
                'is_wishlist',
                true
            );
    }
}
