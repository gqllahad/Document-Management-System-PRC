@extends('layouts.shared_division')

@section('title', 'New Project')

@section('main-content')
<div class="form-page">
    <a href="{{ route('projects.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Back to projects
    </a>

    <div class="page-head">
        <div>
            <h2>New Project</h2>
            <p>{{ Str::upper(auth()->user()->division->name) }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('projects.store') }}" class="card form-grid">
    @csrf

    <div class="field span-2">
        <label for="project_name">Project name</label>
        <input type="text" id="project_name" name="project_name" value="{{ old('project_name') }}" required>
    </div>

    <div class="field">
        <label for="contract_number">Contract number</label>
        <input type="text" id="contract_number" name="contract_number" value="{{ old('contract_number') }}" required>
    </div>

    <div class="field">
        <label for="status_id">Status</label>
        <select id="status_id" name="status_id" required>
            <option value="" disabled selected>Select status..</option>
            @foreach ($statuses as $status)
                <option value="{{ $status->id }}" @selected(old('status_id') == $status->id)>{{ $status->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="approved_budget">Approved budget (ABC)</label>
        <input type="number" step="0.01" min="0" id="approved_budget" name="approved_budget" value="{{ old('approved_budget') }}" required>
    </div>

    <div class="field">
        <label for="contract_price">Contract price <small>optional</small></label>
        <input type="number" step="0.01" min="0" id="contract_price" name="contract_price" value="{{ old('contract_price') }}">
    </div>

    <div class="field">
        <label for="quarter">Quarter</label>
        <select id="quarter" name="quarter" required>
            <option value="" disabled selected>Select quarter..</option>
            @foreach (\App\Models\Project::QUARTERS as $q)
                <option value="{{ $q }}" @selected(old('quarter') == $q)>{{ $q }}</option>
            @endforeach
        </select>
    </div>

    <div class="field">
        <label for="fiscal_year">Fiscal year</label>
        <input type="number" id="fiscal_year" name="fiscal_year" min="2000" max="2100" value="{{ old('fiscal_year', now()->year) }}" required>
    </div>

    <div class="field">
        <label for="contract_date">Contract date <small>optional</small></label>
        <input type="date" id="contract_date" name="contract_date" value="{{ old('contract_date') }}">
    </div>

    <div class="field">
        <label for="delivered_date">Delivered date <small>optional</small></label>
        <input type="date" id="delivered_date" name="delivered_date" value="{{ old('delivered_date') }}">
    </div>

    <div class="field span-2">
        <label for="update">Update <small>optional</small></label>
        <select id="update" name="update">
            <option value="">None</option>
            @foreach (\App\Models\Project::UPDATES as $u)
                <option value="{{ $u }}" @selected(old('update') == $u)>{{ ucwords(str_replace('_', ' ', $u)) }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-actions span-2">
            <a href="{{ route('projects.index') }}" class="btn-sm">Cancel</a>
            <button type="submit" class="btn-primary">Create project</button>
        </div>
    </form>
</div>
@endsection