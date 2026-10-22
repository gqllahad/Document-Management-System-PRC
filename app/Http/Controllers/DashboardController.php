<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use App\Models\Division;
use App\Models\Project;
use App\Models\Status;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

use App\Services\DashboardStats;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

         $user   = Auth::user();
        $scoped = Project::visibleTo($user);

        $quarters     = (clone $scoped)->whereNotNull('quarter')->distinct()->orderBy('quarter')->pluck('quarter');
        $fiscalYears  = (clone $scoped)->whereNotNull('fiscal_year')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year');
        $projectTypes = (clone $scoped)->whereNotNull('project_type')->distinct()->orderBy('project_type')->pluck('project_type');

        $divisions = Division::when($user->role !== 'admin', fn ($q) => $q->where('id', $user->division_id))
            ->orderByRaw("CASE WHEN name = 'ICTS-OD' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        $statuses = Status::orderBy('sort_order')->get();

        $filters = [
            'division' => $request->query('division', 'all'),
            'fiscal'   => $request->query('fiscal', 'all'),
            'type'     => $request->query('type', 'all'),
            'quarter'  => $request->query('quarter', 'all'),
        ];

        $projects = Project::with(['status', 'division', 'latestUpdate'])
            ->visibleTo($user)
            ->when($filters['division'] !== 'all', fn ($q) => $q->where('division_id', $filters['division']))
            ->when($filters['fiscal']   !== 'all', fn ($q) => $q->where('fiscal_year', $filters['fiscal']))
            ->when($filters['type']     !== 'all', fn ($q) => $q->where('project_type', $filters['type']))
            ->when($filters['quarter']  !== 'all', fn ($q) => $q->where('quarter', $filters['quarter']))
            ->latest()
            ->get();

        $deliveredId     = $statuses->firstWhere('name', 'Delivered')?->id;
        $stageCounts     = $projects->countBy('status_id');
        $stats           = DashboardStats::summary($projects, $deliveredId);
        $cardsByDivision = DashboardStats::cardsByDivision($projects, $deliveredId);
        $deliveries      = DashboardStats::deliveries($projects);

        $byQuarter = $projects->whereNotNull('quarter')->groupBy('quarter')
            ->map(fn ($g) => (object) [
                'abc'      => $g->sum('approved_budget'),
                'contract' => $g->where('contract_price', '>', 0)->sum('contract_price'),
            ]);

        $stageRows = $projects->map(fn ($p) => [
            'd' => $p->division_id, 'y' => $p->fiscal_year,
            'q' => $p->quarter,     's' => $p->status_id,
        ])->values();

        $stageByDivision = $projects->groupBy('division_id')->map(fn ($g) => $g->countBy('status_id'));

        $quarterByDivision = $projects->whereNotNull('quarter')->groupBy('division_id')
            ->map(fn ($d) => $d->groupBy('quarter')->map(fn ($g) => [
                'abc'      => (float) $g->sum('approved_budget'),
                'contract' => (float) $g->where('contract_price', '>', 0)->sum('contract_price'),
            ]));

        return view('division.partials.dashboard', compact(
            'divisions', 'statuses', 'projects', 'stageCounts', 'stageRows',
            'stats', 'byQuarter', 'filters', 'fiscalYears',
            'projectTypes', 'quarters', 'deliveries', 'stageByDivision',
            'quarterByDivision', 'cardsByDivision'
        ));
    }

    private function deliveries(Collection $projects): Collection
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
                    $left <= 14     => 'soon',
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
                                    default => $left . ' ' . Str::plural('day', $left) . ' left',
                                },
                ];
            })
            ->sortBy([['order', 'asc'], ['deadline', 'asc']])
            ->values();
    }

    public function trackingProjects()
    {
        // abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        //  $deliveries = $this->deliveries(Project::with('division')->get());
        $deliveries = DashboardStats::deliveries(
            Project::with('division')->visibleTo(Auth::user())->get()
        );

        return view('division.partials.tracking', [
            'deliveries' => $deliveries,
            'overdue'    => $deliveries->where('state', 'overdue')->count(),
            'soon'       => $deliveries->where('state', 'soon')->count(),
        ]);
    }
}
