<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class SecondaryAdmin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $connection = 'tenant';

    protected $table = 'secondary_admins';
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tenant_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Set the database name for the tenant connection
        if (session()->has('tenant')) {
            $tenant = session('tenant');
            if (isset($tenant->database_name)) {
                config(['database.connections.tenant.database' => $tenant->database_name]);
                DB::purge('tenant');
                DB::reconnect('tenant');
            }
        }
    }
} 