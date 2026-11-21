<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ScrapDispatch extends Model
{
    protected $table = 'scrap_dispatch';

    protected $guarded = [];

    protected static function booted(): void
    {
        static::creating(function (self $dispatch): void {
            if (blank($dispatch->public_token)) {
                $dispatch->public_token = (string) Str::uuid();
            }
        });
    }
}
