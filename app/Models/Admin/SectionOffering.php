<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
