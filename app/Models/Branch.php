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

    public static function getAll($id = null)
    {
        if ($id) {
            return self::where('id', $id)->first()?->toArray();
        }

        return self::all();
    }
}
