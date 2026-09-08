<?php

namespace App\Models\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'action',
        'entity_type',
        'entity_id',
        'entity_reference',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' =>
            'array',

        'new_values' =>
            'array',

        'created_at' =>
            'datetime',
    ];


    /*
    |--------------------------------------------------------------------------
    | User
    |--------------------------------------------------------------------------
    */

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Friendly Entity Name
    |--------------------------------------------------------------------------
    */

    public function getEntityNameAttribute(): string
    {
        return class_basename(
            $this->entity_type
        );
    }
}
