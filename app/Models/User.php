<?php

namespace App\Models;

use App\Models\Admin\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;


    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_id',
        'is_active',
        'password',
        'email_verified_at',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected $casts = [
        'email_verified_at' =>
            'datetime',

        'password' =>
            'hashed',

        'is_active' =>
            'boolean',
    ];


    /*
    |--------------------------------------------------------------------------
    | Role
    |--------------------------------------------------------------------------
    */

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Permission
    |--------------------------------------------------------------------------
    */

    public function hasPermission(
        string $permissionKey
    ): bool {
        if (!$this->is_active) {

            return false;
        }


        if (!$this->role) {

            return false;
        }


        if (!$this->role->is_active) {

            return false;
        }


        /*
         * System role receives full access.
         */
        if ($this->role->is_system) {

            return true;
        }


        return $this
            ->role
            ->permissions()
            ->where(
                'permissions.permission_key',
                $permissionKey
            )
            ->where(
                'permissions.is_active',
                true
            )
            ->exists();
    }


    public function hasAnyPermission(
        array $permissionKeys
    ): bool {
        foreach (
            $permissionKeys
            as $permissionKey
        ) {

            if (
                $this->hasPermission(
                    $permissionKey
                )
            ) {

                return true;
            }
        }


        return false;
    }
}
