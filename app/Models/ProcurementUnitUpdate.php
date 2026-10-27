<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProcurementUnitUpdate extends Model
{
    protected $fillable = [
        'procurement_unit_id',
        'division_id',
        'status_id',
        'created_by',
        'update',
        'status_date',
    ];

    protected function casts(): array
    {
        return [
            'status_date' => 'date',
        ];
    }

    public function procurementUnit(): BelongsTo
    {
        return $this->belongsTo(ProcurementUnit::class);
    }

    public function division(): BelongsTo
    {
        return $this->belongsTo(Division::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
