<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SectionOffering extends Model
{
    use HasFactory;
    protected $fillable = [
        'day_id',
        'section_id',
        'start_time',
        'duration_minutes',
        'end_time',
        'max_seats',
        'is_active',
    ];

    protected $casts = [
        'day_id' => 'integer',
        'section_id' => 'integer',
        'duration_minutes' => 'integer',
        'max_seats' => 'integer',
        'is_active' => 'boolean',
    ];

    public function day()
    {
        return $this->belongsTo(
            Day::class,
            'day_id',
            'id'
        );
    }

    public function section()
    {
        return $this->belongsTo(
            Section::class,
            'section_id',
            'id'
        );
    }

    public function enrolments()
    {
        return $this->hasMany(
            Enrolment::class,
            'section_offering_id',
            'id'
        );

    }
    public function subSections(): BelongsToMany
    {
        return $this->belongsToMany(
            SubSection::class,
            'section_offering_sub_section',
            'section_offering_id',
            'sub_section_id'
        )->withTimestamps();
    }
}
