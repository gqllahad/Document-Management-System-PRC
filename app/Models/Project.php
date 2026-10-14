<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;


class Project extends Model
{
    use SoftDeletes;

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


    // variable init
    public function documents(): HasMany
    {
        return $this->hasMany(Documents::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function division(): BelongsTo
{
    return $this->belongsTo(Division::class);
}

// savinsg
    public function getSavingsAttribute(): ?float
    {
        if ($this->contract_price === null) {
            return null;
        }

        return max(0, (float) $this->approved_budget - (float) $this->contract_price);
    }

    // date retrieveal
    protected function casts(): array
    {
        return [
            'contract_date'  => 'date',
            'delivered_date' => 'date',
            'status_date'    => 'date',
        ];
    }

    // update curretn status
    public function updates()
    { 
        return $this->hasMany(ProjectUpdate::class)->latest('status_date')->latest('id'); 
    }
    public function latestUpdate() 
    { 
       return $this->hasOne(ProjectUpdate::class)->latestOfMany('id');
    }

}
