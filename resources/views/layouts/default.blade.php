
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

</head>

<body>

    <header>
        @yield('header')
    </header>

    <main>
        @yield('main-content')
    </main>

    <footer class="site-footer">
        <span>© {{ date('Y') }} Document Management System</span>
        <span>All rights reserved.</span>
    </footer>
@include('partials.login-toast')
</body>

</html>
