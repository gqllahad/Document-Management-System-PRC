
<div class="brand"><div class="logo"><img src="{{ asset('images/logo.jpg') }}" alt=""></div><span>ICTS PPMS</span></div>

<nav class="nav">
    <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : (auth()->user()->division ? route('division.show', strtolower(auth()->user()->division->name)) : url('/')) }}" class="{{ request()->routeIs('division.show', 'admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i>
        <span>Dashboard</span>
    </a>
    <a href="{{ route('projects.index') }}" class="{{ request()->routeIs('projects.index', 'projects.show') ? 'active' : '' }}">
        <i class="bi bi-folder2"></i><span>Procurement Projects</span>
    </a>

    <a href="#"><i class="bi bi-file-earmark-text"></i><span>Procurement Documents</span></a>
    <br><br><br><br><br>
    <p class="nav-label">Division</p>

    <div class="nav-card">
        <span>{{ Str::upper(auth()->user()->division->name ?? '') }}</span>
        <small>Procurement workspace</small>
    </div>

    <a href="#" class="nav-manage"><i class="bi bi-people"></i><span>Manage Users</span></a>

</nav>

<div class="nav nav-bottom">
    <a href="#"><i class="bi bi-person-circle"></i><span>Profile</span></a>
    <form method="POST" action="{{ url('/logout') }}">
        @csrf
        <button type="submit"><i class="bi bi-box-arrow-right"></i><span>Logout</span></button>
    </form>
</div>