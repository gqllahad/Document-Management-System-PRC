<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\Status;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Carbon\Carbon;

class ProjectController extends Controller
{

    private function authorizeCreate(): void
    {
        $user = Auth::user();

        abort_if($user->role === 'admin' || ! $user->division_id, 403);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $projects = Project::with(['status', 'division'])
            ->when($user->role !== 'admin', fn ($q) => $q->where('division_id', $user->division_id))
            ->latest()
            ->paginate(10);

        $statuses = Status::orderBy('sort_order')->get();

        $projects = Project::with(['status', 'division', 'latestUpdate'])
        ->when($user->role !== 'admin', fn ($q) => $q->where('division_id', $user->division_id))
        ->latest()
        ->paginate(10);

        return view('projects.index', compact('projects', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorizeCreate();

        return view('projects.create', ['statuses' => Status::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $this->authorizeCreate();

    // $data = $request->validate([
    //     'project_name'    => 'required|string|max:255',
    //     'contract_number' => 'required|string|max:255|unique:projects,contract_number',
    //     'approved_budget' => 'required|numeric|min:0|max:9999999999999.99',
    //     'contract_price'  => 'nullable|numeric|min:0|max:9999999999999.99',
    //     'contract_date'   => 'nullable|date',
    //     'delivered_date'  => 'nullable|date|after_or_equal:contract_date',
    //     'quarter'         => ['required', Rule::in(Project::QUARTERS)],
    //     'fiscal_year'     => 'required|integer|between:2000,2100',
    //     'status_id'       => 'required|exists:status,id',
    //     'update'          => ['nullable', Rule::in(Project::UPDATES)],
    // ]);

    // $user = Auth::user();
    // $data['division_id'] = $user->division_id;
    // $data['created_by']  = $user->id;

    // $project = Project::create($data);

    // $redirect = redirect()->route('projects.show', $project)->with('success', 'Project created.');

    // if ($project->over_budget) {
    //     $redirect->with('warning', 'Contract price is above the approved budget.');
    // }

    // return $redirect;
    // }

    public function store(Request $request)
    {
        
        $request->merge([
            'approved_budget' => str_replace(',', '', $request->approved_budget),
        ]);

        $data = $request->validate([
            'project_name'    => 'required|string|max:255',
            'approved_budget' => 'required|numeric|min:0',
        ]);

        Project::create($data + [
            'division_id' => Auth::user()->division_id,
            'status_id' => Status::orderBy('sort_order')->value('id'),
        ]);

        return redirect()->route('projects.index');
    }

    /**
     * Display the specified resource.
     */
   public function show(Project $project)
    {
        $user = Auth::user();

        abort_if($user->role !== 'admin' && $project->division_id !== $user->division_id, 403);

        $project->load(['division', 'status', 'documents.uploader']);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit()
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        logger($request->all());

        $request->merge([
            'contract_price' => str_replace(',', '', $request->contract_price),
        ]);

        $data = $request->validate([
            'contract_number' => 'required|string|max:100|unique:projects,contract_number,' . $project->id,
            'contract_price'  => 'required|numeric|min:0|max:' . $project->approved_budget,
            'contract_date'   => 'required|date',
            'period_type'     => 'required|in:quarter,epa',
            'status_id'       => 'required|exists:statuses,id',
            'update'          => 'required|string|max:255',
            'status_date'     => 'required|date',
            'documents.*'     => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'contract_price.max' => 'Contract price cannot exceed the approved budget (₱' . number_format($project->approved_budget, 2) . ').',
            'contract_price.min' => 'Contract price cannot be negative.',
        ]);

        $date = Carbon::parse($data['contract_date']);

        $project->update([
            'contract_price'  => $data['contract_price'],
            'contract_date'   => $date,
            'contract_number' => ltrim($data['contract_number'], '.'), //'CS-' . now()->format('n-Y') . '-' . 
            'quarter' => $data['period_type'] === 'epa' ? 'EPA/1ST': ['1ST', '2ND', '3RD', '4TH'][$date->quarter - 1],
            'fiscal_year'     => $date->year,
            'status_id'          => $data['status_id'],
        ]);

        foreach ($request->file('documents', []) as $file) {
            $project->documents()->create([
                'name' => $file->getClientOriginalName(),
                'path' => $file->store("projects/{$project->id}", 'public'),
            ]);
        }

        $project->updates()->create([
            'division_id' => $project->division_id,
            'status_id'   => $data['status_id'],
            'created_by'  => Auth::id(),
            'update'      => $data['update'],
            'status_date' => $data['status_date'],
        ]);

        return redirect()->route('projects.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
