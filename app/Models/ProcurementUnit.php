<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcurementUnit extends Model
{
    protected $fillable = [
        'project_id',
        'unit_name',
        'contract_price',
        'contract_number',
        'contract_date',
        'winning_bidder',
        'ntp_date',
        'delivery_period',
        'actual_date_of_delivery',
        'status_id',
        'status_date',
        'update',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'contract_price' => 'decimal:2',
            'contract_date' => 'date',
            'ntp_date' => 'date',
            'actual_date_of_delivery' => 'date',
            'status_date' => 'date',
            'delivery_period' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProjectItem::class);
    }
    public function updates(): HasMany
    {
        return $this->hasMany(ProcurementUnitUpdate::class);
    }

}
