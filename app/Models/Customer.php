<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'customers';

    protected $fillable = [
        'cust_code',
        'company_name',
        'email',
        'country_code',
        'mobile',
        'status',
        'country_id',
        'state_id',
        'city_id',
        'address',
        'gst_no',
        'pincode',
        'buyer',
        'supplier',
        'created_by',
    ];

    protected $casts = [
        'status' => 'integer',
        'country_id' => 'integer',
        'state_id' => 'integer',
        'city_id' => 'integer',
        'buyer' => 'integer',
        'supplier' => 'integer',
        'created_by' => 'integer',
    ];

    /* Status */

    public const STATUS_ACTIVE = 1;
    public const STATUS_DEACTIVE = 2;

    /* Buyer / Supplier */

    public const YES = 1;
    public const NO = 2;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
