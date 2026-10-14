@extends('layouts.shared_division')

@section('title', 'Procurement Projects')
@section('sidebar-class', 'sidebar-division')
@section('sidebar') @include('partials.sidebar-divisions') @endsection
@section('division', Str::upper(auth()->user()->division->name ?? ''))

@section('main-content')
<div class="page-head">
    <div>
        <h2>Projects</h2>
        <p>{{ auth()->user()->role === 'admin' ? 'All divisions' : Str::upper(auth()->user()->division->name) }}</p>
    </div>

</div>

<div class="card table-card">
    <div class="table-head">
        <div>
            <h3>Projects<span class="count">{{ $projects->total() }}</span></h3>
             <small>Manage all procurement projects</small>
        </div>
        <div class="table-tools">
            @if (auth()->user()->role !== 'admin')
                <button type="button" class="btn-primary" data-drawer-open="projectDrawer">
                    <i class="bi bi-plus-lg"></i> New project
                </button>
            @endif

            <div class="search">
                <i class="bi bi-search"></i>
                <input type="search" id="projectSearch" placeholder="Search name or email">
            </div>
        </div>
    </div>

    <div class="filter-buttons">
        <button type="button" class="active" data-filter="all">All Projects</button>
        <button type="button" data-filter="awaiting">Awaiting Contract</button>
        <button type="button" data-filter="recorded">Recorded Contract</button>
    </div>

    <div class="table-wrap">
        <table class="table" id="projectTable">
            <thead>
                <tr>
                    <th>Project Name</th>
                    @if (auth()->user()->role === 'admin') <th>Division</th> @endif
                    <th class="num">Approved Budget of Contract</th>
                    <th class="num">Contract Price</th>
                    <th class="num">Savings</th>
                    <th>Status</th>
                    <th>Contract No.</th>
                    <th>Contract Date</th>
                    <th>Quarter</th>
                    <th>Update</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td>
                            <strong>{{ $project->project_name }}</strong>
                            <small class="tb_small">PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}</small>
                        </td>
                        @if (auth()->user()->role === 'admin')
                            <td>{{ Str::upper($project->division->name) }}</td>
                        @endif
                        <td class="num">₱{{ number_format($project->approved_budget, 2) }}</td>
                        <td class="num {{ $project->contract_price === null ? 'text-muted' : '' }}">{{ $project->contract_price !== null ? '₱' . number_format($project->contract_price, 2) : 'Pending' }}</td>
                        <td class="num">{{ $project->savings !== null ? '₱' . number_format($project->savings, 2) : '—' }}</td>
                       <td class="update-cell">
                            @php $u = $project->latestUpdate; @endphp
                            @if ($u)
                                @if ($u->created_at->gt(now()->subDays(3)))
                                    <span class="badge-new">New</span>
                                @endif
                                <span class="update-text" title="{{ $u->update }}">{{ $u->update }}</span>
                                <small class="update-date">{{ $u->status_date->format('m/d/Y') }}</small>
                            @else
                                <span class="text-muted">No updates yet</span>
                            @endif
                        </td>
                        <td>{{ $project->contract_number? '' . $project->contract_number : '—' }}</td>
                        <td>{{ $project->contract_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $project->quarter ?? '—' }}{{ $project->fiscal_year ? ' · ' . $project->fiscal_year : '' }}</td>
                        <td><span class="pill pill-role">{{ $project->status->name ?? '—' }}</span></td>
                        @if (auth()->user()->role !== 'admin')
                        <td class="actions">
                            <button class="btn-sm"><i class="bi bi-folder2-open"></i>View</button>
                            <button type="button" class="btn-sm" data-continue
                                data-action="{{ route('projects.update', $project) }}"
                                data-name="{{ $project->project_name }}"
                                data-abc="{{ number_format($project->approved_budget, 2) }}"
                                data-code="PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}"
                                data-contract="CON-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}"
                                data-created="{{ $project->contract_date?->format('M d, Y') ?? '' }}"
                                data-status="{{ $project->status_id }}">
                                <i class="bi bi-pencil-square"></i> Update
                            </button>
                            
                            <!-- <a href="{{ route('projects.show', $project) }}" class="btn-sm"><i class="bi bi-eye"></i> View</a> -->
                        </td>
                         @endif
                    </tr>
                @empty
                    <tr class="empty"><td colspan="10">No projects yet.</td></tr>
                @endforelse
                @if ($projects->isNotEmpty())
                    <tr class="empty no-results" hidden><td colspan="10">No matching projects.</td></tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrap">{{ $projects->links() }}</div>

@if (auth()->user()->role !== 'admin')
<div class="drawer-backdrop {{ $errors->any() ? 'open' : '' }}" id="projectDrawerBackdrop"></div>

<!-- stage 1 -->
<aside class="drawer {{ $errors->any() ? 'open' : '' }}" id="projectDrawer" aria-hidden="true">
    <form method="POST" action="{{ route('projects.store') }}" class="drawer-form">
        @csrf

        <div class="drawer-head">
            <div>
                <h3>Create project</h3>
                <p>Start a procurement project</p>
            </div>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="drawer-body">
            <span class="drawer-badge">{{ Str::upper(auth()->user()->division->name ?? '') }} workspace</span>
            @include('partials.stepper', ['current' => 1])

            <div class="field">
                <label for="project_name">Project name <b>*</b></label>
                <input type="text" id="project_name" name="project_name" value="{{ old('project_name') }}" placeholder="Enter project name" required>
                @error('project_name') <small class="err">{{ $message }}</small> @enderror
            </div>

            <div class="field">
                <label for="approved_budget">Approved Budget for the Contract (ABC) <b>*</b></label>
                <div class="input-prefix">
                    <span>₱</span>
                    <input type="text" id="approved_budget" name="approved_budget" value="{{ old('approved_budget') }}" placeholder="0.00" inputmode="decimal" required>
                </div>
                @error('approved_budget') <small class="err">{{ $message }}</small> @enderror
            </div>

            <div class="drawer-note">
                <i class="bi bi-info-circle"></i>
                Contract details, documents and status updates can be added when you return to this project.
            </div>
        </div>

        <div class="drawer-foot">
            <button type="button" class="btn-sm" data-drawer-close>Cancel</button>
            <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Create project</button>
        </div>
    </form>
</aside>

<!-- stage 2 -->
<aside class="drawer" id="continueDrawer" aria-hidden="true">
    <form method="POST" id="continueForm" enctype="multipart/form-data" class="drawer-form">
        @csrf @method('PUT')

        <div class="drawer-head">
            <div>
                <h3>Project continuation</h3>
                <p>Add contract details to your existing project</p>
            </div>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="drawer-body">
            <span class="drawer-badge">{{ Str::upper(auth()->user()->division->name ?? '') }} workspace</span>
            @include('partials.stepper', ['current' => 2])

            <div class="sec-title">Recorded project <small><i class="bi bi-lock"></i> Read-only</small></div>
            <div class="recorded">
                <small>PROJECT NAME</small>
                <strong id="cName"></strong>
                <span><span id="cCode"></span> · Created <span id="cCreated"></span></span>
                <div class="recorded-abc">
                    <div><small>Approved budget (ABC)</small><small>Approved Budget for the Contract</small></div>
                    <b>₱<span id="cAbc"></span></b>
                </div>
            </div>

            <hr>
            <div class="sec-title">Contract details <small class="tag-warn">To be completed</small></div>

            <div class="field">
                <label>Contract number</label> 
                <input type="text" id="cContract" name="contract_number" placeholder="Enter contract number..">
            </div>

            <div class="field">
                <label for="contract_price">Contract price <b>*</b></label>
                <div class="input-prefix">
                    <span>₱</span>
                    <input type="text" id="contract_price" name="contract_price" placeholder="0.00" inputmode="decimal" required>
                </div>
                @error('contract_price') <small class="err">{{ $message }}</small> @enderror
            </div>

            <div class="row-2">
                <div class="field">
                    <label for="contract_date">Contract date <b>*</b></label>
                    <input type="date" id="contract_date" name="contract_date" required>
                </div>
                <div class="field">
                    <label>Period <small class="tag">Auto</small></label>
                    <div class="seg">
                        <label><input type="radio" name="period_type" value="quarter" checked><span>Quarter</span></label>
                        <label><input type="radio" name="period_type" value="epa"><span>EPA</span></label>
                    </div>
                    <div class="period-out" id="periodOut">—</div>
                </div>
            </div>

            <hr>
            <div class="sec-title">Progress</div>
            <p class="muted">Keep the project status current for your team.</p>

            <div class="field">
                <label for="status_id">Update status</label>
                <select id="status_id" name="status_id" required>
                    @foreach ($statuses as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label for="update">Latest update <b>*</b></label>
                <textarea id="update" name="update" rows="3" placeholder="e.g. Notice to proceed issued, delivery scheduled" required></textarea>
                <input type="date" id="update_date" name="status_date" required>
            </div>

            <hr>
            <div class="sec-title">Documents <small id="fileCount">0 files</small></div>
            <p class="muted">Notice of award, signed contract, notice to proceed, etc.</p>

            <label class="dropzone" for="documents">
                <i class="bi bi-upload"></i>
                <b>Click to upload files</b>
                <small>PDF, DOCX or images</small>
                <input type="file" id="documents" name="documents[]" multiple accept=".pdf,.doc,.docx,image/*" hidden>
            </label>
        </div>

        <div class="drawer-foot">
            <button type="button" class="btn-sm" data-drawer-close>Cancel</button>
            <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Save contract details</button>
        </div>
    </form>
</aside>
@endif

@endsection


