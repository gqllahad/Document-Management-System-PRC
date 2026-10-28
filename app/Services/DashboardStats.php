<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DashboardStats
{
    public static function summary(Collection $projects, ?int $deliveredId): array
    {
        $recorded = $projects->filter(
            fn ($p) => $p->procurementUnits->contains(
                fn ($unit) => filled($unit->contract_number)
            )
        );

        $recordedAbc = $recorded->sum('approved_budget');

        $contracted = $projects->filter(
            fn ($p) => $p->procurementUnits->contains(
                fn ($unit) => (float) $unit->contract_price > 0
            )
        );

        $contractPrice = $contracted->sum(
            fn ($p) => $p->procurementUnits->sum('contract_price')
        );

        $savings = $contracted->sum(
            fn ($p) => max(
                0,
                (float) $p->approved_budget -
                (float) $p->procurementUnits->sum('contract_price')
            )
        );

        $delivered = $projects->filter(
            fn ($p) => $p->procurementUnits->isNotEmpty()
                && $p->procurementUnits->every(
                    fn ($unit) => ($unit->status->name ?? '') === 'Delivered'
                )
        )->count();

        return [
            'total' => $projects->count(),
            'delivered' => $delivered,
            'ongoing' => $projects->count() - $delivered,
            'abc' => $projects->sum('approved_budget'),
            'contract_price' => $contractPrice,
            'savings' => $savings,
            'savings_percentage' => $recordedAbc > 0
                ? ($savings / $recordedAbc) * 100
                : 0,
        ];
    }

    public static function cardRows(Collection $projects, ?int $deliveredId): Collection
    {
        return $projects->map(function ($p) {
            $units = $p->procurementUnits;

            $contract = (float) $units->sum('contract_price');

            $hasContract = $units->contains(
                fn ($unit) => filled($unit->contract_number)
                    && (float) $unit->contract_price > 0
            );

            $contracted = $units->contains(
                fn ($unit) => (float) $unit->contract_price > 0
            );

            $delivered = $units->isNotEmpty()
                && $units->every(
                    fn ($unit) => ($unit->status->name ?? '') === 'Delivered'
                );

            return [
                'd' => $p->division_id,
                'y' => $p->fiscal_year,
                'q' => $p->quarter,
                'delivered' => $delivered ? 1 : 0,
                'abc' => (float) $p->approved_budget,
                'contract' => $hasContract ? $contract : 0,
                'contracted' => $contracted ? 1 : 0,
                'savings' => $hasContract
                    ? max(0, (float) $p->approved_budget - $contract)
                    : 0,
            ];
        })->values();
    }

    public static function deliveries(Collection $projects): Collection
    {
        $today = now()->startOfDay();

        return $projects
            ->flatMap(function ($p) use ($today) {
                return $p->procurementUnits
                    ->filter(fn ($unit) => $unit->ntp_date && $unit->delivery_period > 0)
                    ->map(function ($unit) use ($p, $today) {
                        $ntp = Carbon::parse($unit->ntp_date)->startOfDay();

                        $deadline = $ntp->copy()->addDays($unit->delivery_period);

                        $actual = $unit->actual_date_of_delivery
                            ? Carbon::parse($unit->actual_date_of_delivery)->startOfDay()
                            : null;

                        $left = (int) $today->diffInDays($deadline, false);

                        $elapsed = (int) $ntp->diffInDays($actual ?? $today, false);

                        $pct = (int) min(
                            100,
                            max(0, round($elapsed / $unit->delivery_period * 100))
                        );

                        $state = match (true) {
                            (bool) $actual => 'delivered',
                            $left < 0 => 'overdue',
                            $left <= 14 => 'soon',
                            default => 'ongoing',
                        };

                        return (object) [
                            'id' => $unit->id,
                            'project_id' => $p->id,
                            'unit_name' => $unit->unit_name,
                            'division_id' => $p->division_id,
                            'quarter' => $p->quarter,
                            'year' => $p->fiscal_year,
                            'name' => $p->project_name,
                            'division' => $p->division->name ?? '—',
                            'contract_no' => $unit->contract_number ?? 'No contract no.',
                            'period' => $unit->delivery_period,
                            'ntp' => $ntp,
                            'deadline' => $deadline,
                            'actual' => $actual,
                            'pct' => $pct,
                            'state' => $state,
                            'order' => [
                                'overdue' => 0,
                                'soon' => 1,
                                'ongoing' => 2,
                                'delivered' => 3,
                            ][$state],
                            'label' => match (true) {
                                (bool) $actual => 'Delivery recorded',
                                $left < 0 => abs($left) . ' ' . Str::plural('day', abs($left)) . ' overdue',
                                $left === 0 => 'Due today',
                                default => $left . ' ' . Str::plural('day', $left) . ' left',
                            },
                        ];
                    });
            })
            ->sortBy([['order', 'asc'], ['deadline', 'asc']])
            ->values();
    }
}