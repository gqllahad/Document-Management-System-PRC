<div class="brand"><div class="logo"><img src="{{ asset('images/logo.jpg') }}" alt=""></div><span>@yield('division')</span></div>

<nav class="nav">
    <a href="{{ url('/division/'.strtolower($__env->yieldContent('division'))) }}"
        class="{{ request()->is('division/*') && !request()->is('division/*/*') ? 'active' : '' }}">
        <i class="bi bi-speedometer2"></i><span>Dashboard</span>
    </a>
    <a href="#"><i class="bi bi-cloud-arrow-up"></i><span>Upload Document</span></a>
    <a href="#"><i class="bi bi-folder2-open"></i><span>My Documents</span></a>
</nav>

<div class="nav nav-bottom">
    <a href="#"><i class="bi bi-person-circle"></i><span>Profile</span></a>
    <form method="POST" action="{{ url('/logout') }}">
        @csrf
        <button type="submit"><i class="bi bi-box-arrow-right"></i><span>Logout</span></button>
    </form>
</div>