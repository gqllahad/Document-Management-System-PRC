<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Document Management System')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/main.css', 'resources/js/main.js'])
</head>

<body>
    <div class="app">
        <aside class="sidebar @yield('sidebar-class')">
            @yield('sidebar')
        </aside>

        <div class="content">
            <header class="topbar">
                <button class="icon-btn" id="sidebarToggle" aria-label="Toggle sidebar">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="topbar-title">@yield('title')</h1>
                <div class="topbar-user">
                    <span class="user-name">{{ auth()->user()->name ?? 'Guest' }}</span>
                    <span class="role-badge">{{ ucfirst(auth()->user()->role ?? '') }}</span>
                </div>
                @yield('header')
            </header>

            @if (session('error'))
            <div class="alert-error">{{ session('error') }}</div>
            @endif

            <main>@yield('main-content')</main>

            <footer>@yield('footer')</footer>
        </div>
    </div>
</body>

</html>