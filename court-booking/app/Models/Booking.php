<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'event_name',
        'description',
        'equipment_request',
        'number_of_participants',
        'start_date',
        'end_date',
        'status'
    ];

    protected $casts = [
        'date' => 'datetime',
        'time_slot' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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