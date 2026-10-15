<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GradeCategory extends Model
{
    use SoftDeletes;

    protected $table = 'grade_categories';

    protected $fillable = [
        'name',
        'created_by',
    ];
}
