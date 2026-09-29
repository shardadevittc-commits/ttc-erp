<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;
use App\Models\Size;


class Unit extends Model
{
    use SoftDeletes;

    protected $table = 'units';

    protected $fillable = [
        'unit_name',
        'short_name',
        'created_by',
    ];

    protected $casts = [
        'created_by' => 'integer',
    ];

    /* Relationships */

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(Size::class, 'unit_id');
    }
}
