<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = ['name'];

     // divs
    public function scopeVisibleTo($q, User $user)
    {
        return $user->role === 'admin' ? $q : $q->where('id', $user->division_id);
    }


}
