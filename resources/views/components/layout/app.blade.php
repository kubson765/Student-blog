<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Blog' }}</title>
    @vite(['resources/css/app.scss', 'resources/js/app.js'])
</head>
<body class="bg-light d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">StuBlog</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/">Strona Główna</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/login">Zaloguj</a></li>
                    <li class="nav-item"><a class="nav-link" href="/register">Zarejestruj</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container my-5 flex-grow-1">
        {{ $slot }}
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            <small>&copy; {{ date('Y') }} StuBlog. Wszelkie prawa zastrzeżone.</small>
        </div>
    </footer>

</body>
</html>
