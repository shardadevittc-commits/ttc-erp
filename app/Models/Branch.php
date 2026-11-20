<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'company_name',
        'logo',
        'favicon',
        'gst',
        'pan',
        'address',
        'certificate_desc',
        'email',
        'website',
        'phone',
    ];
}
