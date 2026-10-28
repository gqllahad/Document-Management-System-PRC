<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ProjectSummary
{
    public static function make(Project $project): array
    {
        $units = $project->procurementUnits;

        $latestUnitUpdate = $units
            ->flatMap(fn ($unit) => $unit->updates->map(fn ($update) => [
                'update'    => $update,
                'unit_name' => $unit->unit_name,
            ]))
            ->sortByDesc(fn ($item) => $item['update']->created_at)
            ->first();

        $year = fn ($unit) => $project->fiscal_year
            ?? $unit->contract_date?->year
            ?? $project->contract_date?->year;

        $filterQuarters = $units->map(fn ($u) => $u->quarter ?? $project->quarter)
            ->filter()->unique()->values();

        $filterYears = $units->map($year)->filter()->unique()->values();

        $periods = $units
            ->map(fn ($u) => collect([$u->quarter ?? $project->quarter, $year($u)])
                ->filter(fn ($v) => filled($v))->implode(' · '))
            ->filter()->unique()->values();

        if ($periods->isEmpty()) {
            $fallback = collect([
                $project->quarter,
                $project->fiscal_year ?? $project->contract_date?->year,
            ])->filter()->implode(' · ');

            if ($fallback !== '') {
                $periods->push($fallback);
            }
        }

        $totalUnits      = $units->count();
        $contractedUnits = $units->filter(fn ($u) => filled($u->contract_number))->count();

        $priced        = $units->whereNotNull('contract_price');
        $contractPrice = $priced->isNotEmpty() ? $priced->sum('contract_price') : $project->contract_price;
        $savings       = $totalUnits > 0 && $priced->count() === $totalUnits
            ? $project->approved_budget - $contractPrice
            : $project->savings;

        // $obligated = $totalUnits > 0 && $units->every(fn ($u) => StatusFlow::reached($u->status?->name, 'Obligated'));
        $obligated = $totalUnits > 0 && $units->every(fn ($u) => StatusFlow::reached($u->status?->name, 'Obligated'));

        $docs = $project->documents->map(fn ($d) => [
            'id'   => $d->id,
            'name' => $d->file_name ?? basename($d->file_path ?? ''),
            'url'  => $d->file_path ? url(Storage::url($d->file_path)) : null,
            'size' => $d->file_path && Storage::disk('public')->exists($d->file_path)
                ? Storage::disk('public')->size($d->file_path)
                : null,
        ])->toJson();

        $history = $units
            ->flatMap(fn ($unit) => $unit->updates->map(fn ($h) => [
                '_sort'      => $h->created_at?->timestamp ?? 0,
                'unit'       => $unit->unit_name,
                'status'     => $h->status->name ?? '—',
                'date'       => $h->status_date?->format('M d, Y'),
                'note'       => $h->update,
                'at'         => $h->created_at?->diffForHumans(),
                'created_at' => $h->created_at?->toISOString(),
            ]))
            ->sortByDesc('_sort')
            ->map(fn ($h) => collect($h)->except('_sort'))
            ->values()
            ->toJson();

        $unitsView = $units->map(fn ($unit) => [
            'name'            => $unit->unit_name,
            'contract_number' => $unit->contract_number,
            'contract_price'  => $unit->contract_price,
            'contract_date'   => $unit->contract_date?->format('M d, Y'),
            'winning_bidder'  => $unit->winning_bidder,
            'ntp_date'        => $unit->ntp_date?->format('M d, Y'),
            'delivery_period' => $unit->delivery_period,
            'actual_delivery' => $unit->actual_date_of_delivery?->format('M d, Y'),
            'status'          => $unit->status?->name,
            'status_date'     => $unit->status_date?->format('Y-m-d'),
            'items'           => $unit->items->map(fn ($i) => [
                'name'        => $i->item_name,
                'description' => $i->description,
                'quantity'    => $i->quantity,
                'unit'        => $i->unit,
                'unit_price'  => $i->unit_price,
            ])->values(),
        ])->values()->toJson();

        $unitsEdit = $units->map(fn ($unit) => [
            'id'             => $unit->id,
            'name'           => $unit->unit_name,
            'contractNumber' => $unit->contract_number,
            'price'          => $unit->contract_price,
            'contractDate'   => $unit->contract_date?->format('Y-m-d'),
            'winningBidder'  => $unit->winning_bidder,
            'status'         => $unit->status?->name,
            'status_id'      => $unit->status_id,
            'status_date'    => $unit->status_date?->format('Y-m-d'),
            'actualDelivery' => $unit->actual_date_of_delivery?->format('Y-m-d'),
        ])->toJson();

        return [
            'code'             => 'PRJ-' . $project->created_at->year . '-' . str_pad($project->id, 3, '0', STR_PAD_LEFT),
            'units'            => $units,
            'scheduled'        => $units->filter(fn ($u) => $u->contract_number),
            'latestUnitUpdate' => $latestUnitUpdate,
            'updatedDate'      => data_get($latestUnitUpdate, 'update.status_date')?->format('Y-m-d'),
            'filterQuarters'   => $filterQuarters,
            'filterYears'      => $filterYears,
            'periods'          => $periods,
            'totalUnits'       => $totalUnits,
            'contractedUnits'  => $contractedUnits,
            'pendingUnits'     => $totalUnits - $contractedUnits,
            'contractPrice'    => $contractPrice,
            'savings'          => $savings,
            'docs'             => $docs,
            'history'          => $history,
            'unitsView'        => $unitsView,
            'unitsEdit'        => $unitsEdit,
            'savingsTransferable' => $obligated && ($savings ?? 0) > 0,
            'hasDocs'             => $project->documents->isNotEmpty(),
        ];
    }
}