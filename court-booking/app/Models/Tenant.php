<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasFactory, HasDatabase, HasDomains;

    protected $fillable = [
        'name',
        'email',
        'domain',
        'status',
        'password',
        'database_name',
        'is_premium',
        'plan',
        'plan_started_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_premium' => 'boolean',
        'plan_started_at' => 'datetime',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
            'email',
            'domain',
            'status',
            'password',
            'database_name',
            'is_premium',
            'plan',
            'plan_started_at',
        ];
    }
} 