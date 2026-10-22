<?php

namespace App\Http\Controllers;

use App\Models\Division;
use App\Models\Project;
use App\Models\Status;
use App\Services\DashboardStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class DivisionController extends Controller
{

    // public function admin()
    // {
    //     $user = Auth::user();

    //     $divisions = Division::orderByRaw("CASE WHEN name = 'ICTS-OD' THEN 0 ELSE 1 END")->orderBy('name')->get();

    //     return view('admin.dashboard', compact('divisions'));
    // }

    public function show($name)
    {
        $division  = Division::whereRaw('LOWER(name) = ?', [$name])->firstOrFail();
        $divisions = Division::orderByRaw("CASE WHEN name = 'ICTS-OD' THEN 0 ELSE 1 END")->orderBy('name')->get();

        $user = Auth::user();

        abort_unless(
            $user->role === 'admin' || (int) $user->division_id === (int) $division->id,
            403
        );

        $filters = [
            'division' => $division->id,
            'fiscal'   => request('fiscal', 'all'),
            'type'     => request('type', 'all'),
            'quarter'  => request('quarter', 'all'),
        ];

        $projects = Project::with(['status', 'division', 'latestUpdate'])
            ->where('division_id', $division->id)
            ->when($filters['fiscal']   !== 'all', fn ($q) => $q->where('fiscal_year', $filters['fiscal']))
            ->when($filters['type']     !== 'all', fn ($q) => $q->where('project_type', $filters['type']))
            ->when($filters['quarter']  !== 'all', fn ($q) => $q->where('quarter', $filters['quarter']))
            ->latest()
            ->get();

        $statuses    = Status::orderBy('sort_order')->get();
        $deliveredId = $statuses->firstWhere('name', 'Delivered')?->id;

        $stats           = DashboardStats::summary($projects, $deliveredId);
        $cardsByDivision = DashboardStats::cardsByDivision($projects, $deliveredId);
        $deliveries      = DashboardStats::deliveries($projects);

        $base         = Project::where('division_id', $division->id);
        $quarters     = (clone $base)->whereNotNull('quarter')->distinct()->orderBy('quarter')->pluck('quarter');
        $fiscalYears  = (clone $base)->whereNotNull('fiscal_year')->distinct()->orderByDesc('fiscal_year')->pluck('fiscal_year');
        $projectTypes = (clone $base)->whereNotNull('project_type')->distinct()->orderBy('project_type')->pluck('project_type');

        $stageCounts = $projects->countBy('status_id');

        $stageRows = $projects->map(fn ($p) => [
            'd' => $p->division_id, 'y' => $p->fiscal_year,
            'q' => $p->quarter,     's' => $p->status_id,
        ])->values();

        $byQuarter = $projects->whereNotNull('quarter')->groupBy('quarter')
            ->map(fn ($g) => (object) [
                'abc'      => $g->sum('approved_budget'),
                'contract' => $g->where('contract_price', '>', 0)->sum('contract_price'),
            ]);

        $stageByDivision = $projects->groupBy('division_id')->map(fn ($g) => $g->countBy('status_id'));

        $quarterByDivision = $projects->whereNotNull('quarter')->groupBy('division_id')
            ->map(fn ($d) => $d->groupBy('quarter')->map(fn ($g) => [
                'abc'      => (float) $g->sum('approved_budget'),
                'contract' => (float) $g->where('contract_price', '>', 0)->sum('contract_price'),
            ]));

        return view("division.{$name}.index", compact(
            'division', 'divisions', 'statuses', 'projects', 'stats', 'cardsByDivision',
            'filters', 'quarters', 'fiscalYears', 'projectTypes', 'deliveries',
            'stageCounts', 'stageRows', 'byQuarter', 'stageByDivision', 'quarterByDivision'
        ));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Division $division)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Division $division)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Division $division)
    {
        //
    }
}
