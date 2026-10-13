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

    @if (auth()->user()->role !== 'admin')
        <a href="{{ route('projects.create') }}" class="btn-primary" style="text-decoration:none;">
            <i class="bi bi-plus-lg"></i> New project
        </a>
        
    @endif
</div>

<div class="card table-card">
    <div class="table-head">
        <h3>Procurement Projects</h3>
        <span class="count">{{ $projects->total() }}</span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Project Name</th>
                    @if (auth()->user()->role === 'admin') <th>Division</th> @endif
                    <th class="num">ABC</th>
                    <th class="num">Contract Price</th>
                    <th class="num">Savings</th>
                    <th>Status</th>
                    <th>Contract No.</th>
                    <th>Delivery Date</th>
                    <th>Quarter</th>
                    <th>Update</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td><strong>{{ $project->project_name }}</strong></td>
                        @if (auth()->user()->role === 'admin')
                            <td>{{ Str::upper($project->division->name) }}</td>
                        @endif
                        <td class="num">₱{{ number_format($project->approved_budget, 2) }}</td>
                        <td class="num">{{ $project->contract_price !== null ? '₱' . number_format($project->contract_price, 2) : '—' }}</td>
                        <td class="num">{{ $project->savings !== null ? '₱' . number_format($project->savings, 2) : '—' }}</td>
                        <td><span class="pill pill-role">{{ $project->status->name ?? '—' }}</span></td>
                        <td>{{ $project->contract_number }}</td>
                        <td>{{ $project->delivered_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $project->quarter }}{{ $project->fiscal_year ? ' · ' . $project->fiscal_year : '' }}</td>
                        <td>{{ $project->update ? ucwords(str_replace('_', ' ', $project->update)) : '—' }}</td>
                        <td class="actions">
                            <a href="{{ route('projects.show', $project) }}" class="btn-sm"><i class="bi bi-eye"></i> View</a>
                        </td>
                    </tr>
                @empty
                    <tr class="empty"><td colspan="11">No projects yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrap">{{ $projects->links() }}</div>
@endsection


