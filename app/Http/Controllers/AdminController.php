<?php

namespace App\Http\Controllers;

use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Division;
use App\Models\Project;
use App\Models\Status;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AdminController extends Controller
{

    public function dashboard(Request $request)
    {
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

        $filters = [
            'division' => $request->query('division', 'all'),
            'fiscal'   => $request->query('fiscal', 'all'),
            'type'     => $request->query('type', 'all'),
        ];

        // filters
        $divisions = Division::orderByRaw(
            "CASE WHEN name = 'ICTS-OD' THEN 0 ELSE 1 END"
        )->orderBy('name')->get();

        $statuses = Status::orderBy('sort_order')->get();

        $fiscalYears  = Project::whereNotNull('fiscal_year')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year');
        $projectTypes = Project::whereNotNull('project_type')->distinct()->orderBy('project_type')->pluck('project_type');

        $projects = Project::with(['status', 'division', 'latestUpdate'])
            ->when($filters['division'] !== 'all', fn ($q) => $q->where('division_id', $filters['division']))
            ->when($filters['fiscal']   !== 'all', fn ($q) => $q->where('fiscal_year', $filters['fiscal']))
            ->when($filters['type']     !== 'all', fn ($q) => $q->where('project_type', $filters['type']))
            ->latest()
            ->get();

       // stages    
        $stageCounts = $projects->countBy('status_id');

        // top right
        $deliveredId = $statuses->firstWhere('name', 'Delivered')?->id;
        $delivered = $stageCounts[$statuses->firstWhere('name', 'Delivered')?->id] ?? 0;

        $recordedProjects = $projects->whereNotNull('contract_number');
        $recordedAbc = $recordedProjects->sum('approved_budget');
        $recordedContractPrice = $recordedProjects->where('contract_price', '>', 0)->sum('contract_price');
        $totalSavings = $recordedProjects
            ->where('contract_price', '>', 0)
            ->sum(fn ($p) => $p->approved_budget - $p->contract_price);

        $stats = [
            'total'              => $projects->count(),
            'delivered'          => $delivered,
            'ongoing'            => $projects->count() - $delivered,
            'abc'                => $projects->sum('approved_budget'),
            'contract_price'     => $recordedContractPrice,
            'savings'            => $totalSavings,
            'savings_percentage' => $recordedAbc > 0 ? ($totalSavings / $recordedAbc) * 100 : 0,
        ];

        $byQuarter = $projects->whereNotNull('quarter')
            ->groupBy('quarter')
            ->map(fn ($g) => (object) [
                'abc'      => $g->sum('approved_budget'),
                'contract' => $g->where('contract_price', '>', 0)->sum('contract_price'),
            ]);

            // dd($byQuarter);

        // delivery bottom rihgt 
        // $today = now()->startOfDay();

        $deliveries = $this->deliveries($projects);

            // filterss
        $stageByDivision = $projects->groupBy('division_id')
                                    ->map(fn ($g) => $g->countBy('status_id'));

        $quarterByDivision = $projects->whereNotNull('quarter')
                            ->groupBy('division_id')
                            ->map(fn ($d) => $d->groupBy('quarter')->map(fn ($g) => [
                                'abc'      => (float) $g->sum('approved_budget'),
                                'contract' => (float) $g->where('contract_price', '>', 0)->sum('contract_price'),
                            ]));

        $cardsByDivision = $projects->groupBy('division_id')->map(function ($g) use ($deliveredId) {
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

        return view('admin.dashboard', compact(
            'divisions', 'statuses', 'projects', 'stageCounts',
            'stats', 'byQuarter', 'filters', 'fiscalYears', 
            'projectTypes', 'deliveries','stageByDivision',
            'quarterByDivision', 'cardsByDivision',
        ));
    }

     public function manageUsers()
    {
        abort_unless(Auth::check(), 403);

        abort_unless(Auth::user()->role === 'admin', 403);

        $users = User::with('division')
            ->where('role', '!=', 'admin')
            ->orderBy('name')
            ->get();

        
        return view('admin.manage_users', compact('users'));
    }


    public function updateRole(Request $request, User $user)
    {
        abort_if($user->role === 'admin', 403);

        $data = $request->validate([
            'role' => 'required|in:head,fund_controller',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update($data);

        return back()->with('success', 'Role updated for ' . $user->name . '.');
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
        abort_unless(Auth::check() && Auth::user()->role === 'admin', 403);

         $deliveries = $this->deliveries(Project::with('division')->get());

        return view('admin.tracking', [
            'deliveries' => $deliveries,
            'overdue'    => $deliveries->where('state', 'overdue')->count(),
            'soon'       => $deliveries->where('state', 'soon')->count(),
        ]);
    }

}
