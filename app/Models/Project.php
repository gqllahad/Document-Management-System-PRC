<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SoftDeletes;

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
        'update',
        'remarks',
        'status_date',

    ];

    public const QUARTERS = ['EPA/1ST', 'EPA/2ND', '1ST', '2ND', '3RD', '4TH'];

    public const UPDATES = ['installed', 'renewed', 'delivered', 'successful_bid', 'cancelled', 'pending', 'obligated'];

    public function documents(): HasMany
    {
        return $this->hasMany(Documents::class);
    }
}
