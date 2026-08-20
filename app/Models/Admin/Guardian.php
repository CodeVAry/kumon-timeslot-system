<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\User;

class Guardian extends Authenticatable
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
        'normalized_email',
        'normalized_phone',
    ];


    protected $casts = [
        'is_active' => 'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | User Relationship
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Students Relationship
    |--------------------------------------------------------------------------
    */

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


    /*
    |--------------------------------------------------------------------------
    | Normalize Email
    |--------------------------------------------------------------------------
    */

    public static function normalizeEmail($email)
    {
        if (!$email) {
            return null;
        }

        return strtolower(
            trim($email)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Normalize Phone
    |--------------------------------------------------------------------------
    */

    public static function normalizePhone($phone)
    {
        if (!$phone) {
            return null;
        }

        return preg_replace(
            '/[^0-9]/',
            '',
            $phone
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Automatically Normalize Email
    |--------------------------------------------------------------------------
    */

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] =
            $value;

        $this->attributes['normalized_email'] =
            self::normalizeEmail($value);
    }


    /*
    |--------------------------------------------------------------------------
    | Automatically Normalize Phone
    |--------------------------------------------------------------------------
    */

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] =
            $value;

        $this->attributes['normalized_phone'] =
            self::normalizePhone($value);
    }
}
