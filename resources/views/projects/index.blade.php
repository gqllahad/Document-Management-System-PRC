@extends('layouts.shared_division')

@section('title', 'Procurement Projects')
@section('sidebar-class', auth()->user()->role === 'admin' ? 'sidebar-admin' : 'sidebar-division')
@section('sidebar') @include(auth()->user()->role === 'admin' ? 'partials.sidebar-admin' : 'partials.sidebar-divisions') @endsection
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
                <input type="search" id="projectSearch" placeholder="Search project..">
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
                            <td><span class="pill pill-div">{{ Str::upper($project->division->name) }}</span></td>
                        @endif
                        <td class="num">₱{{ number_format($project->approved_budget, 2) }}</td>
                        <td class="num {{ $project->contract_price === null ? 'text-muted' : '' }}">{{ $project->contract_price !== null ? '₱' . number_format($project->contract_price, 2) : 'Pending' }}</td>
                        <td class="num">{{ $project->savings !== null ? '₱' . number_format($project->savings, 2) : '—' }}</td>
                       <td class="update-cell">
                            @php $u = $project->latestUpdate; @endphp
                            @if ($u)
                                @if ($u->created_at->gt(now()->subDays(2)))
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
                       
                        <td class="actions">
                            <button type="button" class="btn-sm" data-view
                                data-name="{{ $project->project_name }}"
                                data-code="PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}"
                                data-created="{{ $project->created_at->format('M d, Y') }}"
                                data-division="{{ $project->division->name ?? '' }}"
                                data-status="{{ $project->status->name ?? '' }}"
                                data-abc="{{ $project->approved_budget }}"
                                data-price="{{ $project->contract_price }}"
                                data-contract-number="{{ $project->contract_number }}"
                                data-contract-date="{{ $project->contract_date?->format('M d, Y') }}"
                                data-delivered="{{ $project->delivered_date?->format('M d, Y') }}"
                                data-period="{{ $project->quarter ? $project->quarter . ($project->fiscal_year ? ' · ' . $project->fiscal_year : '') : '' }}"
                                data-update="{{ $project->latestUpdate->update ?? '' }}"
                                data-status-date="{{ $project->latestUpdate?->status_date?->format('M d, Y') }}"
                                data-docs="{{ $project->documents->map(fn ($d) => [
                                    'name' => $d->name,
                                    'url'  => Storage::url($d->path),
                                    'size' => Storage::disk('public')->exists($d->path) ? Storage::disk('public')->size($d->path) : null,
                                ])->toJson() }}">
                                <i class="bi bi-folder2-open"></i> View
                            </button>
                             @if (auth()->user()->role !== 'admin')
                            <button type="button" class="btn-sm" data-continue
                                data-action="{{ route('projects.update', $project) }}"
                                data-id="{{ $project->id }}"
                                data-name="{{ $project->project_name }}"
                                data-abc="{{ number_format($project->approved_budget, 2) }}"
                                data-code="PRJ-{{ $project->created_at->year }}-{{ str_pad($project->id, 3, '0', STR_PAD_LEFT) }}"
                                data-created="{{ $project->created_at->format('M d, Y') }}"
                                data-status="{{ $project->status_id }}"
                                data-contract-number="{{ $project->contract_number }}"
                                data-price="{{ $project->contract_price }}"
                                data-contract-date="{{ $project->contract_date?->format('Y-m-d') }}"
                                data-quarter="{{ $project->quarter }}">
                                <i class="bi bi-pencil-square"></i> Update
                            </button>
                            
                            <!-- <a href="{{ route('projects.show', $project) }}" class="btn-sm"><i class="bi bi-eye"></i> View</a> -->
                        
                         @endif
                         </td>
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

@php $stage1Err = $errors->has('project_name') || $errors->has('approved_budget'); @endphp

<div class="drawer-backdrop {{ $stage1Err ? 'open' : '' }}" id="projectDrawerBackdrop"></div>

<!-- view -->
 <aside class="drawer" id="viewDrawer" aria-hidden="true">
    <div class="drawer-form">
        <div class="drawer-head">
            <div>
                <small class="eyebrow">Project overview</small>
                <h3 id="vName"></h3>
                <p><span id="vCode"></span> · Created <span id="vCreated"></span></p>
            </div>
            <button type="button" class="drawer-close" data-drawer-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="drawer-body">
            <div class="v-top">
                <span class="drawer-badge" id="vDivision"></span>
                <span class="v-pill" id="vStatusPill"><i class="bi bi-circle"></i> <span id="vStatus"></span></span>
            </div>

            <div class="v-fin" id="vFin">
                <small>Financial overview</small>
                <span class="v-lbl">Contract price</span>
                <strong id="vPrice"></strong>
                <div class="v-abc"><span>Approved budget <em>ABC</em></span><b id="vAbc"></b></div>
                <div class="v-bar"><i id="vBar"></i></div>
                <div class="v-diff" id="vDiff"></div>
            </div>

           <div class="v-sec"><i class="bi bi-file-earmark-text"></i> Contract details <small class="v-rec" id="vRecTag"><i class="bi bi-check2"></i> Recorded</small></div>

            <div class="c-num">
                <small>Contract number</small>
                <strong id="vContractNumber"></strong>
            </div>

            <div class="c-grid">
                <div>
                    <span class="c-lbl"><i class="bi bi-calendar3"></i> Contract date</span>
                    <b id="vContractDate"></b>
                </div>
                <div>
                    <span class="c-lbl"><i class="bi bi-calendar2-range"></i> Procurement period</span>
                    <b id="vPeriodQ"></b>
                    <small id="vPeriodY"></small>
                </div>
            </div>

            <div class="c-row">
                <span class="c-lbl"><i class="bi bi-truck"></i> Delivery date</span>
                <b id="vDelivered"></b>
            </div>

            <div class="v-sec"><i class="bi bi-arrow-repeat"></i> Progress</div>
            <div class="v-progress">
                <small>Current Update</small>
                <strong id="vStatus2"></strong>
                <span class="v-date"><i class="bi bi-calendar3"></i> <span id="vStatusDate"></span></span>
                <div class="v-note" id="vNote">
                    <div class="v-note-head">
                        <span><i class="bi bi-chat-left-text"></i> Latest status</span>
                        <time id="vNoteDate"></time>
                    </div>
                    <p id="vUpdate"></p>
                </div>
                <button type="button" class="link-toggle" id="vHistoryBtn" hidden></button>
                <ul class="timeline" id="vHistory" hidden></ul>
            </div>

            <div class="v-sec"><i class="bi bi-paperclip"></i> Documents <small class="v-count" id="vDocCount">0</small></div>
            <ul class="v-docs" id="vDocs"></ul>
            <div class="v-none" id="vNone" hidden><i class="bi bi-folder-x"></i> No documents uploaded</div>
        </div>

        <div class="drawer-foot">
            @if(auth()->user()->role !== 'admin')
                <button type="button" class="btn-primary btn-save" id="vUpdateBtn"><i class="bi bi-pencil"></i> Update project</button>
            @else
            <button type="button" class="btn-sm v-remarks"><i class="bi bi-pencil"></i> Add remarks</button>
            @endif
        </div>
    </div>
</aside>

@if (auth()->user()->role !== 'admin')
<!-- stage 1 -->
<aside class="drawer {{ $stage1Err ? 'open' : '' }}" id="projectDrawer" aria-hidden="true">
    <form method="POST" action="{{ route('projects.store') }}" class="drawer-form" id="createForm">
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

            <div class="field">
                <label for="project_type">Project type <b>*</b></label>
                <select id="project_type" name="project_type" required>
                    <option value="" disabled {{ old('project_type') ? '' : 'selected' }}>Select type</option>
                    @foreach (\App\Models\Project::PROJECT_TYPES as $v)
                        <option value="{{ $v }}" @selected(old('project_type') === $v)>{{ $v }}</option>
                    @endforeach
                </select>
                @error('project_type') <small class="err">{{ $message }}</small> @enderror
            </div>

            <div class="row-2">
                <div class="field">
                    <label for="source_funds">Source of funds <b>*</b></label>
                    <select id="source_funds" name="source_funds" required>
                        <option value="" disabled {{ old('source_funds') ? '' : 'selected' }}>Select source</option>
                        @foreach (\App\Models\Project::SOURCE_FUNDS as $v)
                            <option value="{{ $v }}" @selected(old('source_funds') === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('source_funds') <small class="err">{{ $message }}</small> @enderror
                </div>

                <div class="field">
                    <label for="expenses_classes">Expense class <b>*</b></label>
                    <select id="expenses_classes" name="expenses_classes" required>
                        <option value="" disabled {{ old('expenses_classes') ? '' : 'selected' }}>Select class</option>
                        @foreach (\App\Models\Project::EXPENSES_CLASSES as $v)
                            <option value="{{ $v }}" @selected(old('expenses_classes') === $v)>{{ $v }}</option>
                        @endforeach
                    </select>
                    @error('expenses_classes') <small class="err">{{ $message }}</small> @enderror
                </div>
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

            <div class="sec-title">Contract details <small class="tag-warn" id="contractTag">To be completed</small>
                <button type="button" class="link-toggle" id="unlockBtn" hidden>Edit</button>
            </div>

            <div class="field">
                <label for="cContract">Contract number</label>
                <input type="text" id="cContract" name="contract_number" placeholder="Enter contract number..">
                <small class="err" id="contractErr" hidden></small>
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
                    <label for="quarter">Period <small class="tag">Auto</small></label>
                    <select id="quarter" name="quarter" required>
                        <option value="" disabled selected>Select period</option>
                        <option value="EPA">EPA</option>
                        <option value="1ST">1ST Quarter</option>
                        <option value="2ND">2ND Quarter</option>
                        <option value="3RD">3RD Quarter</option>
                        <option value="4TH">4TH Quarter</option>
                    </select>
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
                <label for="update">Current status <b>*</b></label>
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
            <span class="drawer-note"><i class="bi bi-shield-check"></i>The original project name and approved budget remain unchanged.</span>
        </div>

        <div class="drawer-foot">
             <button type="button" class="btn-ghost" data-drawer-close>Cancel</button>
            <button type="submit" class="btn-primary btn-save">
                <i class="bi bi-check2-circle"></i> Save contract details
            </button>
        </div>
    </form>
</aside>

<!-- review -->
<dialog class="review" id="reviewModal">
    <div class="review-head">
        <span class="review-ico"><i class="bi bi-clipboard-check"></i></span>
        <div>
            <h3>Review before saving</h3>
            <p>Please double-check the details below.</p>
        </div>
    </div>

    <div class="review-body">
        <div class="review-warn" id="rvWarn" hidden>
            <i class="bi bi-exclamation-triangle"></i><span></span>
        </div>
        <dl class="review-list" id="rvList"></dl>
    </div>

    <div class="review-foot">
        <button type="button" class="btn-sm" data-review-back>Go back &amp; edit</button>
        <button type="button" class="btn-primary" data-review-confirm><i class="bi bi-check-lg"></i> Confirm &amp; save</button>
    </div>
</dialog>
@endif

@endsection


