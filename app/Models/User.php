<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Admin\Role;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'role_id',
        'is_active',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(
            Role::class,
            'role_id',
            'id'
        );
    }


    public function hasPermission(string $permissionKey): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->role) {
            return false;
        }

        if (!$this->role->is_active) {
            return false;
        }

        // Protected Super Admin receives complete access.
        if ($this->role->is_system) {
            return true;
        }

        return $this->role
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

    public function hasAnyPermission(array $permissionKeys): bool
    {
        foreach ($permissionKeys as $permissionKey) {
            if ($this->hasPermission($permissionKey)) {
                return true;
            }
        }

        return false;
    }
}
