<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class DashboardStats
{
    public static function summary(Collection $projects, ?int $deliveredId): array
    {
        $recorded    = $projects->whereNotNull('contract_number');
        $recordedAbc = $recorded->sum('approved_budget');
        $contracted  = $recorded->where('contract_price', '>', 0);
        $savings     = $contracted->sum(fn ($p) => $p->approved_budget - $p->contract_price);
        $delivered   = $projects->where('status_id', $deliveredId)->count();

        return [
            'total'              => $projects->count(),
            'delivered'          => $delivered,
            'ongoing'            => $projects->count() - $delivered,
            'abc'                => $projects->sum('approved_budget'),
            'contract_price'     => $contracted->sum('contract_price'),
            'savings'            => $savings,
            'savings_percentage' => $recordedAbc > 0 ? ($savings / $recordedAbc) * 100 : 0,
        ];
    }

    public static function cardsByDivision(Collection $projects, ?int $deliveredId): Collection
    {
        return $projects->groupBy('division_id')->map(function ($g) use ($deliveredId) {
            $rec = $g->whereNotNull('contract_number')->where('contract_price', '>', 0);

            return [
                'total'      => $g->count(),
                'delivered'  => $g->where('status_id', $deliveredId)->count(),
                'abc'        => (float) $g->sum('approved_budget'),
                'contract'   => (float) $rec->sum('contract_price'),
                'contracted' => $g->where('contract_price', '>', 0)->count(),
                'savings'    => (float) $rec->sum(fn ($p) => $p->approved_budget - $p->contract_price),
            ];
        });
    }

    public static function deliveries(Collection $projects): Collection
    {
        $today = now()->startOfDay();

        return $projects
            ->filter(fn ($p) => $p->ntp_date && $p->delivery_period)
            ->map(function ($p) use ($today) {
                $ntp      = Carbon::parse($p->ntp_date)->startOfDay();
                $deadline = $ntp->copy()->addDays($p->delivery_period);
                $actual   = $p->actual_date_of_delivery ? Carbon::parse($p->actual_date_of_delivery)->startOfDay() : null;
                $left     = (int) $today->diffInDays($deadline, false);
                $elapsed  = (int) $ntp->diffInDays($actual ?? $today, false);
                $pct      = (int) min(100, max(0, round($elapsed / $p->delivery_period * 100)));

                $state = match (true) {
                    (bool) $actual => 'delivered',
                    $left < 0      => 'overdue',
                    $left <= 14    => 'soon',
                    default        => 'ongoing',
                };

                return (object) [
                    'id'          => $p->id,
                    'division_id' => $p->division_id,
                    'quarter'     => $p->quarter,
                    'year'        => $p->fiscal_year,
                    'name'        => $p->project_name,
                    'division'    => $p->division->name ?? '—',
                    'contract_no' => $p->contract_number ?? 'No contract no.',
                    'period'      => $p->delivery_period,
                    'ntp'         => $ntp,
                    'deadline'    => $deadline,
                    'actual'      => $actual,
                    'pct'         => $pct,
                    'state'       => $state,
                    'order'       => ['overdue' => 0, 'soon' => 1, 'ongoing' => 2, 'delivered' => 3][$state],
                    'label'       => match (true) {
                        (bool) $actual => 'Delivery recorded',
                        $left < 0      => abs($left) . ' ' . Str::plural('day', abs($left)) . ' overdue',
                        $left === 0    => 'Due today',
                        default        => $left . ' ' . Str::plural('day', $left) . ' left',
                    },
                ];
            })
            ->sortBy([['order', 'asc'], ['deadline', 'asc']])
            ->values();
    }
}