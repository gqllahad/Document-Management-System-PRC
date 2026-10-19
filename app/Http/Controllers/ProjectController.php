<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\Status;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\Documents;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ProjectController extends Controller
{

    private function authorizeCreate(): void
    {
        $user = Auth::user();

        abort_if($user->role === 'admin' || ! $user->division_id, 403);
    }

    // admin view
    public function adminIndex()
    {
        $user = Auth::user();
        
        $projects = Project::with(['division', 'status', 'latestUpdate', 'documents'])
            ->latest()
            ->paginate(15);
        
        $base = Project::query()
        ->when($user->role !== 'admin', fn ($q) => $q->where('division_id', $user->division_id));

        $statuses = Status::all();
        $divisions = Division::orderBy('name')->get();
        

        $counts = [
            'all'      => (clone $base)->count(),
            'pending' => (clone $base)->whereNull('contract_number')->count(),
            'recorded' => (clone $base)->whereNotNull('contract_number')->count(),
        ];

        return view('projects.index', compact('projects', 'statuses', 'divisions', 'counts'));
    }

    // check contract validation (ajax someting)
    public function checkContract(Request $request)
    {
        $taken = Project::withTrashed()
            ->where('contract_number', ltrim((string) $request->number, '.'))
            ->when($request->ignore, fn ($q, $id) => $q->where('id', '!=', $id))
            ->exists();

        return response()->json(['taken' => $taken]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $projects = Project::with(['status', 'division', 'latestUpdate'])
        ->when($user->role !== 'admin', fn ($q) => $q->where('division_id', $user->division_id))
        ->latest()
        ->paginate(10);

        $statuses = Status::orderBy('sort_order')->get();

        $divisions = Division::orderBy('name')->get();

        $counts = [
            'all'      => (clone $projects)->count(),
            'pending' => (clone $projects)->whereNull('contract_number')->count(),
            'recorded' => (clone $projects)->whereNotNull('contract_number')->count(),
        ];

        return view('projects.index', compact('projects', 'statuses', 'divisions', 'counts'));
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
            // 'project_type'     => ['required', Rule::in(Project::PROJECT_TYPES)],
            'source_funds'     => ['required', Rule::in(Project::SOURCE_FUNDS)],
            // 'winning_bidder' => ['nullable', 'string', 'max:255'],
            // 'delivery_period' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        Project::create($data + [
            'division_id' => Auth::user()->division_id,
            'created_by'  => Auth::id(),
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
        // logger($request->all());\

        abort_if(Auth::user()->role === 'admin' || $project->division_id !== Auth::user()->division_id, 403);

        $request->merge([
            'contract_price' => str_replace(',', '', $request->contract_price),
        ]);

        $data = $request->validate([
            'contract_number' => 'required|string|max:100|unique:projects,contract_number,' . $project->id,
            'contract_price'  => 'required|numeric|min:0|max:' . $project->approved_budget,
            'contract_date'   => 'required|date',
            'quarter'           => ['required', Rule::in(['EPA', '1ST', '2ND', '3RD', '4TH'])],
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
            'quarter' => $data['quarter'],
            'fiscal_year'     => $date->year,
            'status_id'          => $data['status_id'],
        ]);

        foreach ($request->file('documents', []) as $file) {
                $project->documents()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $file->store("projects/{$project->id}", 'public'),
                    'user_id' => Auth::id(),
                    'division_id' => $project->division_id,
                ]);
            }

        $project->updates()->create([
            'division_id' => $project->division_id,
            'status_id'   => $data['status_id'],
            'created_by'  => Auth::id(),
            'update'      => $data['update'],
            'status_date' => $data['status_date'],
        ]);

        return redirect()->route('projects.index')->with('success', 'Successfully updated project ('.  $project->project_name . ').');
    }

    // stage 3
    public function updateSchedule(Request $request, Project $project)
    {

        abort_if(Auth::user()->role === 'admin' || $project->division_id !== Auth::user()->division_id, 403);
        abort_if(! $project->contract_date, 422, 'Record the contract first.');

        $data = $request->validate([
            'ntp_date'        => ['required', 'date', 'after_or_equal:' . Carbon::parse($project->contract_date)->toDateString()],
            'delivery_period' => ['required', 'integer', 'between:1,120'],
            'status_id'       => 'required|exists:statuses,id',
            'update'          => 'required|string|max:255',
            'status_date'     => 'required|date',
            'documents.*'     => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ]);

        foreach ($request->file('documents', []) as $file) {
                $project->documents()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $file->store("projects/{$project->id}", 'public'),
                    'user_id' => Auth::id(),
                    'division_id' => $project->division_id,
                ]);
            }

        $project->updates()->create([
            'division_id' => $project->division_id,
            'status_id'   => $data['status_id'],
            'created_by'  => Auth::id(),
            'update'      => $data['update'],
            'status_date' => $data['status_date'],
        ]);

        return redirect()->route('projects.index')->with('success', 'Schedule saved (' . $project->project_name . ').');
    }

    // delete upload
    public function destroyDocument(Documents $document)
    {
        $user = Auth::user();
        
        if (!$user->division_id ||
            $document->division_id != $user->division_id) {
            abort(403, 'Unauthorized action.');
        }

        if ($document->file_path) {
            Storage::disk('public')->delete($document->file_path);
        }

        $document->delete();

       return request()->expectsJson() ? response()->json(['ok' => true]) : back()->with('success', 'Document deleted successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy()
    {
        //
    }
}
