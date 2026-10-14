@extends('layouts.default_main')

@section('title', 'Manage Users')
@section('sidebar-class', 'sidebar-admin')
@section('sidebar') @include('partials.sidebar-admin') @endsection

@section('main-content')
<div class="page-head">
    <div>
        <h2>Manage Users</h2>
        <p>Manage registered users and assign their roles.</p>
    </div>

    <div>
        <button>
            Add user
        </button>
    </div>
    <div class="search">
        <i class="bi bi-search"></i>
        <input type="search" id="userSearch" placeholder="Search name or email">
    </div>
</div>

<div class="card table-card">
    <div class="table-head">
        <h3>Registered Users</h3>
        <span class="count">{{ $users->count() }}</span>
    </div>

    <div class="table-wrap">
        <table class="table" id="userTable">
            <thead>
                <tr>
                    <th>User</th>
                    <th>Division</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                <tr>
                    <td>
                        <div class="user-cell">
                            <span class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            <div>
                                <strong>{{ $user->name }}</strong>
                                <small>{{ $user->email }}</small>
                            </div>
                        </div>
                    </td>
                    <td>{{ $user->division?->name ? Str::upper($user->division->name) : '—' }}</td>
                    <td><span class="pill pill-role">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span></td>
                    <td><span class="pill status-{{ $user->status }}">{{ ucfirst($user->status) }}</span></td>
                    <td class="actions">
                        <button type="button" class="btn-sm manage-user"
                            data-action="{{ route('admin.users.role', $user) }}"
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-role="{{ $user->role }}"
                            data-status="{{ $user->status }}">
                            <i class="bi bi-sliders"></i> Manage
                        </button>
                    </td>
                </tr>
                @empty
                <tr class="empty">
                    <td colspan="5">No registered users found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<dialog id="roleModal" class="modal">
    <form method="POST" id="roleForm">
        @csrf
        @method('PATCH')

        <div class="modal-head">
            <div class="user-cell">
                <span class="avatar" id="mAvatar"></span>
                <div>
                    <strong id="mName"></strong>
                    <small id="mEmail"></small>
                </div>
            </div>
            <button type="button" class="icon-btn" data-close aria-label="Close"><i class="bi bi-x-lg"></i></button>
        </div>

        <div class="modal-body">
            <p class="modal-label">Assign role</p>

            <label class="role-option">
                <input type="radio" name="role" value="head">
                <span class="role-card">
                    <i class="bi bi-person-badge"></i>
                    <span><strong>Head</strong><small>Oversees the division's procurement records</small></span>
                </span>
            </label>

            <label class="role-option">
                <input type="radio" name="role" value="fund_controller">
                <span class="role-card">
                    <i class="bi bi-cash-coin"></i>
                    <span><strong>Fund Controller</strong><small>Handles budget and fund-related records</small></span>
                </span>
            </label>

            <p class="modal-label">Account status</p>

            <div class="segmented">
                <label>
                    <input type="radio" name="status" value="active">
                    <span><i class="bi bi-check-circle"></i> Active</span>
                </label>
                <label>
                    <input type="radio" name="status" value="inactive">
                    <span><i class="bi bi-slash-circle"></i> Inactive</span>
                </label>
            </div>

            <p class="modal-warn" id="mWarn" hidden>
                <i class="bi bi-exclamation-triangle"></i> Inactive users can't log in.
            </p>

        </div>

        <div class="modal-foot">
            <button type="button" class="btn-sm" data-close>Cancel</button>
            <button type="submit" class="btn-primary" id="mSave" disabled>Save changes</button>
        </div>
    </form>
</dialog>

@endsection