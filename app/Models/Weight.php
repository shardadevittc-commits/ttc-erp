<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Weight extends Model
{
    use SoftDeletes;

    protected $table = 'weights';

    protected $fillable = [
        'vehicle_id',
        'truckno',
        'cust_id',
        'formlist',
        'rstno',
        'loadedweight',
        'tareweight',
        'vehicleentryslip',
        'weighmentslip',
        'material_weight',
        'unload_tare_weight',
        'unload_loaded_weight',
        'first_weight',
        'second_weight',
        'created_by',
    ];

    protected $casts = [
        'loadedweight' => 'decimal:2',
        'tareweight' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'cust_id', 'id');
    }

    public function materialIn()
    {
        return $this->belongsTo(GateAndGaadi::class, 'vehicle_id', 'id');
    }
}
