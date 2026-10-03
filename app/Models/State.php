<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Country;
use App\Models\City;

class State extends Model
{
    protected $table = 'states';

    protected $fillable = [
        'country_id',
        'name',
        'code',
        'status',
    ];

    protected $casts = [
        'country_id' => 'integer',
        'status' => 'boolean',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'state_id');
    }
}
