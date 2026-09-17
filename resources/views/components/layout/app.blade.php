<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'My Blog') }}</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    <!-- ============================================ -->
    <!-- NAVIGATION                                    -->
    <!-- ============================================ -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <!-- Brand / Logo -->
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">
                <i class="bi bi-journal-richtext me-2"></i>StuBlog
            </a>

            <!-- Hamburger (mobile) -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Collapsible menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <!-- Lewa strona: linki nawigacyjne -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('posts.index') ? 'active' : '' }}"
                            href="{{ route('posts.index') }}">
                            <i class="bi bi-journal-text me-1"></i>Wpisy
                        </a>
                    </li>
                    @auth
                        @if (auth()->user()->hasVerifiedEmail())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('posts.create') ? 'active' : '' }}"
                                    href="{{ route('posts.create') }}">
                                    <i class="bi bi-plus-circle me-1"></i>Dodaj wpis
                                </a>
                            </li>
                        @endif
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('posts.anonymous.create') }}">
                                <i class="bi bi-incognito me-1"></i>Anonimowy wpis
                            </a>
                        </li>
                    @endauth
                </ul>

                <!-- Prawa strona: konto użytkownika -->
                <ul class="navbar-nav">
                    @auth
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                                    href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2 me-1"></i>Dashboard
                                </a>
                            </li>

                            @if (auth()->user()->isAdmin())
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}"
                                        href="{{ route('admin.dashboard') }}">
                                        <i class="bi bi-shield-lock me-1"></i>Admin
                                    </a>
                                </li>
                            @endif

                            {{-- reszta --}}
                            @if (auth()->user()->isModerator())
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('moderation.index') }}">
                                        <i class="bi bi-shield-check me-1"></i>Moderacja
                                    </a>
                                </li>
                            @endif
                        @endauth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('password.change') }}">
                                        <i class="bi bi-key me-2"></i>Change Password
                                    </a>
                                </li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}"
                                href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}"
                                href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i>Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- ============================================ -->
    <!-- MAIN CONTENT                                  -->
    <!-- ============================================ -->
    <main class="flex-grow-1">
        {{ $slot }}
    </main>

    <!-- ============================================ -->
    <!-- FOOTER                                        -->
    <!-- ============================================ -->
    <footer class="bg-light py-4 mt-auto border-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <small class="text-muted">
                        &copy; {{ date('Y') }} <strong>StuBlog</strong>. All rights reserved.
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
