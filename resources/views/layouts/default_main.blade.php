<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite('resources/css/main.css')
    @vite('resources/js/main.js')
</head>


<body>
    <header>
        @yield('header')
    </header>

    <main>
        @yield('main-content')
    </main>

    <footer>
        @yield('footer')
    </footer>
    
</body>
</html>