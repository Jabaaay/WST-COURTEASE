<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class TenantUser extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $fillable = ['first_name', 'email', 'password', 'address', 'contact_number'];
    
    protected $connection = 'tenant';

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


