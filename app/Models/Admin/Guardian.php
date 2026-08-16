<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Guardian extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }

    public function students()
    {
        return $this->belongsToMany(
            Student::class,
            'guardian_student',
            'guardian_id',
            'student_id'
        )
            ->withPivot([
                'relationship',
                'is_primary',
                'is_emergency_contact',
            ])
            ->withTimestamps();
    }
}
