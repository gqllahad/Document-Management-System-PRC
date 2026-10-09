<div class="brand"><i class="bi bi-file-earmark-text"></i><span>DMS</span></div>

<nav class="nav">
    <a href="{{ url('/division') }}" class="{{ request()->is('division') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="#"><i class="bi bi-folder2-open"></i><span>All Projects</span></a>

    <p class="nav-label">Divisions</p>
    <a href="{{ url('/division/niisd') }}" class="{{ request()->is('division/niisd') ? 'active' : '' }}"><i class="bi bi-diagram-3"></i><span>NIISD</span></a>
    <a href="{{ url('/division/database') }}" class="{{ request()->is('division/database') ? 'active' : '' }}"><i class="bi bi-database"></i><span>Database</span></a>
    <a href="{{ url('/division/development') }}" class="{{ request()->is('division/development') ? 'active' : '' }}"><i class="bi bi-hdd-network"></i><span>Development</span></a>

    <p class="nav-label">Admin</p>
    <a href="#"><i class="bi bi-clock-history"></i><span>Activity Logs</span></a>
    <a href="#"><i class="bi bi-people"></i><span>Users</span></a>
</nav>

<div class="nav nav-bottom">
    <a href="#"><i class="bi bi-person-circle"></i><span>Profile</span></a>
    <form method="POST" action="{{ url('/logout') }}">
        @csrf
        <button type="submit"><i class="bi bi-box-arrow-right"></i><span>Logout</span></button>
    </form>
</div>