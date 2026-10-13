<div class="brand"><div class="logo"><img src="{{ asset('images/logo.jpg') }}" alt=""></div><span>ICTS PPMS</span></div>

<nav class="nav">
    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="#"><i class="bi bi-folder2-open"></i><span>All Projects</span></a>

    <p class="nav-label">Divisions</p>
    <a href="{{ url('/division/niisd') }}" class="{{ request()->is('division/niisd') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i><span>NIISD</span></a>
    <a href="{{ url('/division/dmsd') }}" class="{{ request()->is('division/dmsd') ? 'active' : '' }}"><i class="bi bi-database"></i><span>DMSD</span></a>
    <a href="{{ url('/division/sdmd') }}" class="{{ request()->is('division/sdmd') ? 'active' : '' }}"><i class="bi bi-hdd-network"></i><span>SDMD</span></a>
    <a href="{{ url('/division/icts-od') }}" class="{{ request()->is('division/icts-od') ? 'active' : '' }}"><i class="bi bi-building"></i><span>ICTS-OD</span></a>
    <br><br><br>
    
    <p class="nav-label">Admin</p>

    <a href="#"><i class="bi bi-clock-history"></i><span>Activity Logs</span></a>

    
    <a href="{{ route('admin.manage_users') }}" class="{{ request()->routeIs('admin.manage_users') ? 'active' : '' }}"><i class="bi bi-people"></i><span>Manage Users</span></a>
</nav>

<div class="nav nav-bottom">
    <a href="#"><i class="bi bi-person-circle"></i><span>Profile</span></a>
    <form method="POST" action="{{ url('/logout') }}">
        @csrf
        <button type="submit"><i class="bi bi-box-arrow-right"></i><span>Logout</span></button>
    </form>
</div>