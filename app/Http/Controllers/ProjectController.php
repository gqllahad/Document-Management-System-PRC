<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\Status;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

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

        return view('projects.index', compact('projects'));
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
    public function store(Request $request)
    {
        $this->authorizeCreate();

    $data = $request->validate([
        'project_name'    => 'required|string|max:255',
        'contract_number' => 'required|string|max:255|unique:projects,contract_number',
        'approved_budget' => 'required|numeric|min:0|max:9999999999999.99',
        'contract_price'  => 'nullable|numeric|min:0|max:9999999999999.99',
        'contract_date'   => 'nullable|date',
        'delivered_date'  => 'nullable|date|after_or_equal:contract_date',
        'quarter'         => ['required', Rule::in(Project::QUARTERS)],
        'fiscal_year'     => 'required|integer|between:2000,2100',
        'status_id'       => 'required|exists:status,id',
        'update'          => ['nullable', Rule::in(Project::UPDATES)],
    ]);

    $user = Auth::user();
    $data['division_id'] = $user->division_id;
    $data['created_by']  = $user->id;

    $project = Project::create($data);

    $redirect = redirect()->route('projects.show', $project)->with('success', 'Project created.');

    if ($project->over_budget) {
        $redirect->with('warning', 'Contract price is above the approved budget.');
    }

    return $redirect;
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
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
