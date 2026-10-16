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
    ];

    protected $casts = [
        'noofpersons' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class, 'cust_id');
    }
}
