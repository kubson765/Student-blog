<x-layout.app title="Welcome">
    <div class="container py-5">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body py-5">
                        <div class="display-1 text-primary mb-4">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                        <h1 class="display-4 mb-3">Witaj na StuBlog</h1>
                        <p class="lead text-muted">Forum dla studentów, chcących podzielić się swoimi doświadczeniami</p>

                        <div class="mt-5">
                            @auth
                                <a href="{{ route('posts.index') }}" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-journal-text me-1"></i>Forum
                                </a>
                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-speedometer2 me-2"></i>Dashboard
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 me-3">
                                    <i class="bi bi-person-plus me-2"></i>Załóż konto
                                </a>
                                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-5">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Blog Features -->
                <div class="row mt-5 text-start">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-primary">
                                    <i class="bi bi-pencil-square me-2"></i>Dodawaj wpsiy
                                </h5>
                                <p class="card-text text-muted small">Opisuj swoje doświadczenie i przemyślenia na każdy
                                    temat,
                                    związany ze studiami
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="bi bi-chat-dots me-2"></i>Udzielaj się
                                </h5>
                                <p class="card-text text-muted small">Twórz społeczność wchodząc w interakcję z innymi
                                    studentami
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-info">
                                    <i class="bi bi-tags me-2"></i>Organizuj
                                </h5>
                                <p class="card-text text-muted small">Kategoryzuj i oznaczaj swoje posty dla
                                    łatwiejszego odnajdywania</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
