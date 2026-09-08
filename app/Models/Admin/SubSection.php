<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubSection extends Model
{
    protected $fillable = [
        'section_id',
        'sub_section_name',
        'description',
        'is_active',
    ];


    protected $casts = [
        'is_active' =>
            'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Parent Section
    |--------------------------------------------------------------------------
    |
    | Example:
    |
    | Math
    |   ├── 3A
    |   ├── B-D
    |   └── E+
    |--------------------------------------------------------------------------
    */

    public function section(): BelongsTo
    {
        return $this->belongsTo(
            Section::class,
            'section_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Enrolments
    |--------------------------------------------------------------------------
    */

    public function enrolments(): HasMany
    {
        return $this->hasMany(
            Enrolment::class,
            'sub_section_id',
            'id'
        );
    }

    public function sectionOfferings(): BelongsToMany
    {
        return $this->belongsToMany(
            SectionOffering::class,
            'section_offering_sub_section',
            'sub_section_id',
            'section_offering_id'
        )
            ->withTimestamps();
    }
}
