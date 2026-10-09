<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{

    protected $fillable = [
        'division_id',
        'status_id',
        'created_by',
        'project_name',
        'approved_budget',
        'contract_price',
        'contract_number',
        'contract_date',
        'delivered_date',
        'quarter',
        'update_type',
    ];

    public function documents(): HasMany
    {
        return $this->hasMany(Documents::class);
    }
}
