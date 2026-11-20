<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GateAndGaadi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gate_and_gaadi';

    protected $fillable = [
        'tokenno',
        'cust_id',
        'formlist',
        'truckno',
        'vehicleentryslip',
        'transportname',
        'noofpersons',
        'drivername',
        'contactno',
        'created_by',
        'check_out_by',
    ];

    protected $casts = [
        'noofpersons' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'cust_id', 'id');
    }

    public function weights()
    {
        return $this->hasMany(Weight::class, 'vehicle_id', 'id');
    }

    public function dispatchMaterials()
    {
        return $this->hasMany(DispatchMaterial::class, 'gate_id', 'id');
    }

    public function entrySecurity()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    public function exitSecurity()
    {
        return $this->belongsTo(User::class, 'check_out_by', 'id');
    }
}
