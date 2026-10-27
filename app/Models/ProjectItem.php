<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItem extends Model
{
     
    protected $fillable = [
        'procurement_unit_id',
        'item_name',
        'description',
        'quantity',
        'unit',
        'unit_price',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:2',
            'unit_price' => 'decimal:2',
        ];
    }

    public function procurementUnit(): BelongsTo
    {
        return $this->belongsTo(ProcurementUnit::class);
    }
}
