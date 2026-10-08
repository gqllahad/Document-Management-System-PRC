
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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

</body>

</html>
