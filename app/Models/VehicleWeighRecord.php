<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleWeighRecord extends Model
{
    use SoftDeletes;

    protected $table = 'vehicle_weigh_records';

    protected $fillable = [
        'material_in_id',
        'truckno',
        'cust_id',
        'formlist',
        'rstno',
        'loadedweight',
        'tareweight',
        'vehicleentryslip',
        'weighmentslip',
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
        return $this->belongsTo(MaterialIn::class, 'material_in_id', 'id');
    }
}
