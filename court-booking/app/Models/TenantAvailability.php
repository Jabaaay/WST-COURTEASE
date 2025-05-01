<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TenantAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'event_name',
        'description',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime'
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        
        // Set the database name for the tenant connection
        if (session()->has('tenant')) {
            config(['database.connections.tenant.database' => session('tenant')->database_name]);
            $this->setConnection('tenant');
        }
    }
} 
