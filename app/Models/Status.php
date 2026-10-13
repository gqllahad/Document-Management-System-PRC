<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['name', 'sort_order', 'is_final'];
    protected $casts = ['is_final' => 'boolean'];
}
