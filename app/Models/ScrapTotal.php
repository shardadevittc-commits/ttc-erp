<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapTotal extends Model
{
    protected $table = 'scrap_totals';

    protected $fillable = [
        'acid_pickling',
        'short_blasting',
        'pointing',
        'point_discard',
        'bedsawcut',
        'misc',
    ];

    protected $attributes = [
        'acid_pickling' => 0,
        'short_blasting' => 0,
        'pointing' => 0,
        'point_discard' => 0,
        'bedsawcut' => 0,
        'misc' => 0,
    ];

    protected $casts = [
        'acid_pickling' => 'float',
        'short_blasting' => 'float',
        'pointing' => 'float',
        'point_discard' => 'float',
        'bedsawcut' => 'float',
        'misc' => 'float',
    ];
}
