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
use Illuminate\Support\Facades\DB;
use App\Models\ProcurementUnit;

class ProjectController extends Controller
{

    private function authorizeCreate(): void
    {
        $user = Auth::user();

        abort_if($user->role === 'admin' || ! $user->division_id, 403);
    }

    public function adminIndex(Request $request)
    {
        $user = Auth::user();

        // abort_unless(Auth::user()->role === 'admin', 403);
        

        $quarters = Project::QUARTERS;
        
        $base = Project::query()
            ->when($user->role !== 'admin', fn ($q) => $q->where('division_id', $user->division_id));

        $fiscalYears = (clone $base)
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');

        $filtered = (clone $base)
            ->when($request->fiscal_year, fn ($q, $v) => $q->where('fiscal_year', $v));

        $projects = (clone $filtered)
            ->with([
                'status',
                'division',
                'latestUpdate',
                'documents',
                'updates.status',
                'procurementUnits.status',
                'procurementUnits.items',
            ])
            ->latest()
            ->get();

        $statuses  = Status::all();
        $divisions = Division::orderBy('name')->get();

        $counts = [
            'all'      => (clone $filtered)->count(),
            'pending'  => (clone $filtered)->whereNull('contract_number')->count(),
            'recorded' => (clone $filtered)->whereNotNull('contract_number')->count(),
        ];

        return view('projects.index', compact('projects', 'statuses', 'divisions', 'counts', 'quarters', 'fiscalYears'));
    }

    public function checkContract(Request $request)
    {

        $number    = ltrim(trim($request->query('number', '')), '.');
        $projectId = $request->query('project'); //indicator same contract number
        $ignore    = $request->query('ignore');

        $taken = $number !== '' && ProcurementUnit::where('contract_number', $number)
            ->when($projectId, fn ($q) => $q->where('project_id', '!=', $projectId))
            ->when($ignore, fn ($q) => $q->where('id', '!=', $ignore))
            ->exists();

        return response()->json(['taken' => $taken]);
    }
  
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $quarters = Project::QUARTERS;

         $scoped = Project::query()
            ->when($user->role !== 'admin', fn ($q) => $q->where('division_id', $user->division_id));

        $fiscalYears = (clone $scoped)
            ->whereNotNull('fiscal_year')
            ->distinct()
            ->orderByDesc('fiscal_year')
            ->pluck('fiscal_year');

        $filtered = (clone $scoped)
            ->when($request->fiscal_year, fn ($q, $v) => $q->where('fiscal_year', $v));

        $projects = (clone $filtered)
            ->with([
                'status',
                'division',
                'latestUpdate',
                'updates.status',
                'procurementUnits.status',
                'procurementUnits.items',
            ])
            ->latest()
            ->get();

        $statuses  = Status::orderBy('sort_order')->get();
        $divisions = Division::orderByRaw("CASE WHEN name = 'ICTS-OD' THEN 0 ELSE 1 END")->orderBy('name')->get();

        $counts = [
            'all'      => (clone $filtered)->count(),
            'pending'  => (clone $filtered)->whereNull('contract_number')->count(),
            'recorded' => (clone $filtered)->whereNotNull('contract_number')->count(),
        ];

        return view('projects.index', compact('projects', 'statuses', 'divisions', 'counts', 'quarters', 'fiscalYears'));
    }

    public function create()
    {
        $this->authorizeCreate();

        return view('projects.create', ['statuses' => Status::all()]);
    }

    // TODO:: remove
    public function store(Request $request)
    {
        $request->merge([
            'approved_budget' => str_replace(',', '', $request->approved_budget),
        ]);

        if ($request->input('procurement_structure') === 'single') {
            $request->request->remove('units');
        }

        $data = $request->validate([
            'project_name' => 'required|string|max:255',
            'approved_budget' => 'required|numeric|min:0',
            'project_type' => ['required', Rule::in(Project::PROJECT_TYPES)],
            'source_funds' => ['required', Rule::in(Project::SOURCE_FUNDS)],
            'quarter'        => ['nullable', Rule::in(['EPA', '1ST', '2ND', '3RD', '4TH'])],

            'procurement_structure' => 'required|in:single,multiple',
            'units' => 'required_if:procurement_structure,multiple|array|min:2',
            'units.*' => 'required_if:procurement_structure,multiple|nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data) {
            $project = Project::create([
                'project_name' => $data['project_name'],
                'approved_budget' => $data['approved_budget'],
                'project_type' => $data['project_type'],
                'source_funds' => $data['source_funds'],
                'quarter'      => $data['quarter'], 
                'fiscal_year' => now()->year,
                'division_id' => Auth::user()->division_id,
                'created_by' => Auth::id(),
                'status_id' => Status::orderBy('sort_order')->value('id'),
            ]);

            if ($data['procurement_structure'] === 'multiple') {
                foreach ($data['units'] as $unitName) {
                    $project->procurementUnits()->create([
                        'unit_name' => trim($unitName),
                    ]);
                }
            } else {
                $project->procurementUnits()->create([
                    'unit_name' => 'Main Contract',
                ]);
            }
        });

        return redirect()
            ->route('projects.index')
            ->with('success', 'Procurement project created successfully.');
    }

    public function storeUnit(Request $request, Project $project)
    {
        $user = Auth::user();

        abort_unless(
            $user->role === 'admin' ||
            $project->division_id === $user->division_id,
            403
        );

        $data = $request->validate([
            'unit_name' => 'required|string|max:255',
            'contract_number' => 'nullable|string|max:255|unique:procurement_units,contract_number',
            'contract_price' => 'nullable|numeric|min:0|max:' . $project->approved_budget,
            'contract_date' => 'nullable|date',
            'winning_bidder' => 'nullable|string|max:255',
        ]);

        $project->procurementUnits()->create($data);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Procurement unit created successfully.');
    }

    /**
     * Display the specified resource.
     */
   public function show(Project $project)
    {
        $user = Auth::user();

        abort_if($user->role !== 'admin' && $project->division_id !== $user->division_id, 403);

        $project->load([
            'division',
            'status',
            'documents.uploader',
            'procurementUnits.status',
            'procurementUnits.items',
        ]);

        return view('projects.show', compact('project'));
    }

    // procure units
    public function updateUnit(Request $request, ProcurementUnit $procurementUnit)
    {
        $project = $procurementUnit->project;

        abort_if(
            Auth::user()->role === 'admin' ||
            $project->division_id !== Auth::user()->division_id,
            403
        );

        $request->merge([
            'contract_price'  => str_replace(',', '', (string) $request->contract_price) ?: null,
            'contract_number' => ltrim(trim((string) $request->contract_number), '.') ?: null,
        ]);

        $otherUnitsTotal = $project->procurementUnits()
            ->where('id', '!=', $procurementUnit->id)
            ->sum('contract_price');

        $remainingABC = max(0, (float) $project->approved_budget - (float) $otherUnitsTotal);

        $data = $request->validate([
            'contract_number' => [
                'nullable', 'string', 'max:100',
                Rule::unique('procurement_units', 'contract_number')
                    ->where(fn ($q) => $q->where('project_id', '!=', $project->id)),
            ],
            'contract_price' => ['nullable', 'numeric', 'gt:0', 'max:' . $remainingABC],
            'winning_bidder' => 'nullable|string|max:255',
            'contract_date'  => 'nullable|date',
            'status_id'      => 'required|exists:statuses,id',
            'update'         => 'required|string|max:255',
            'status_date'    => 'required|date',
            'documents.*'    => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
        ], [
            'contract_number.unique' => 'This contract number is already used by another project.',
            'contract_price.gt'      => 'Contract price must be greater than ₱0.',
            'contract_price.max'     => 'Contract price exceeds the remaining shared ABC of ₱'
                                        . number_format($remainingABC, 2) . '.',
        ]);

        DB::transaction(function () use ($request, $data, $procurementUnit, $project) {
            $procurementUnit->update([
                'contract_number' => $data['contract_number'] ?? null,
                'contract_price'  => $data['contract_price'] ?? null,
                'winning_bidder'  => $data['winning_bidder'] ?? null,
                'contract_date'   => $data['contract_date'] ?? null,
                'status_id'       => $data['status_id'],
                'status_date'     => $data['status_date'],
                'update'          => $data['update'],
            ]);

            $procurementUnit->updates()->create([
                'division_id' => $project->division_id,
                'status_id'   => $data['status_id'],
                'created_by'  => Auth::id(),
                'update'      => $data['update'],
                'status_date' => $data['status_date'],
            ]);

            // if (!empty($data['quarter'])) {
            //     $project->update(['quarter' => $data['quarter']]);
            // }

            foreach ($request->file('documents', []) as $file) {
                $project->documents()->create([
                    'file_name'   => $file->getClientOriginalName(),
                    'file_path'   => $file->store("projects/{$project->id}", 'public'),
                    'user_id'     => Auth::id(),
                    'division_id' => $project->division_id,
                ]);
            }
        });

        return redirect()
            ->route('projects.index')
            ->with('success', 'Successfully updated procurement unit (' . $procurementUnit->unit_name . ').');
    }


    // procure sched
    public function updateUnitSchedule(Request $request, ProcurementUnit $procurementUnit)
    {
        $project = $procurementUnit->project;

        abort_if(
            Auth::user()->role === 'admin' ||
            $project->division_id !== Auth::user()->division_id,
            403
        );

        $deliveredId = Status::where('name', 'like', '%Delivered%')->pluck('id');

        $data = $request->validate([
            'ntp_date' => 'required|date',
            'delivery_period' => 'required|integer|min:1|max:120',
            'status_id' => 'required|exists:statuses,id',
            'update' => 'required|string|max:255',
            'status_date' => 'required|date',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
            'actual_date_of_delivery' => [Rule::requiredIf(fn () => $deliveredId->contains((int) $request->status_id)),'nullable', 'date', 'after_or_equal:ntp_date',],
        ]);

        if (
            !$procurementUnit->contract_date ||
            $data['ntp_date'] < $procurementUnit->contract_date->format('Y-m-d')
        ) {
            return back()
                ->withErrors([
                    'ntp_date' => 'The NTP date must be on or after the contract date.',
                ])
                ->withInput();
        }

        DB::transaction(function () use ($request, $data, $procurementUnit, $project) {
            $procurementUnit->update([
                'ntp_date' => $data['ntp_date'],
                'delivery_period' => $data['delivery_period'],
                'status_id' => $data['status_id'],
                'status_date' => $data['status_date'],
                'update' => $data['update'],
                'actual_date_of_delivery' => $data['actual_date_of_delivery'] ?? null,
            ]);

            $procurementUnit->updates()->create([
                'division_id' => $project->division_id,
                'status_id' => $data['status_id'],
                'created_by' => Auth::id(),
                'update' => $data['update'],
                'status_date' => $data['status_date'],
            ]);

            foreach ($request->file('documents', []) as $file) {
                $procurementUnit->project->documents()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $file->store(
                        "projects/{$project->id}",
                        'public'
                    ),
                    'user_id' => Auth::id(),
                    'division_id' => $project->division_id,
                ]);
            }
        });

        return back()->with(
            'success',
            'Procurement unit delivery schedule updated successfully.'
        );
    }


    // stage 3
    // public function updateSchedule(Request $request, Project $project)
    // {

    //     abort_if(Auth::user()->role === 'admin' || $project->division_id !== Auth::user()->division_id, 403);
    //     abort_if(! $project->contract_date, 422, 'Record the contract first.');

    //     $data = $request->validate([
    //         'ntp_date'        => ['required', 'date', 'after_or_equal:' . Carbon::parse($project->contract_date)->toDateString()],
    //         'delivery_period' => ['required', 'integer', 'between:1,120'],
    //         'status_id'       => 'required|exists:statuses,id',
    //         'update'          => 'required|string|max:255',
    //         'status_date'     => 'required|date',
    //         'documents'   => 'nullable|array',
    //         'documents.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240',
    //     ]);

    //     $project->update([
    //         'ntp_date'        => $data['ntp_date'],
    //         'delivery_period' => $data['delivery_period'],
    //         'status_id'       => $data['status_id'],
    //     ]);

    //     foreach ($request->file('documents', []) as $file) {
    //             $project->documents()->create([
    //                 'file_name' => $file->getClientOriginalName(),
    //                 'file_path' => $file->store("projects/{$project->id}", 'public'),
    //                 'user_id' => Auth::id(),
    //                 'division_id' => $project->division_id,
    //             ]);
    //         }

    //     $project->updates()->create([
    //         'division_id' => $project->division_id,
    //         'status_id'   => $data['status_id'],
    //         'created_by'  => Auth::id(),
    //         'update'      => $data['update'],
    //         'status_date' => $data['status_date'],
    //     ]);

    //     return redirect()->route('projects.index')->with('success', 'Schedule saved (' . $project->project_name . ').');
    // }

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
