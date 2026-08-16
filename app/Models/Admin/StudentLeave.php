<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class StudentLeave extends Model
{
    protected $fillable = [
        'student_id',
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
        'start_date' => 'date',
        'expected_return_date' => 'date',
        'actual_return_date' => 'date',
        'returned_early' => 'boolean',
    ];


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


    public function scopeCurrent($query)
    {
        $today =
            now()->toDateString();


        return $query
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


    public function scopeUpcoming($query)
    {
        return $query
            ->whereDate(
                'start_date',
                '>',
                now()->toDateString()
            )
            ->whereNull(
                'actual_return_date'
            );
    }


    public function scopeCompleted($query)
    {
        return $query
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
