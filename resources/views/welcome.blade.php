<x-layout.app title="Welcome">
    <div class="container py-5">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-body py-5">
                        <div class="display-1 text-primary mb-4">
                            <i class="bi bi-journal-richtext"></i>
                        </div>
                        <h1 class="display-4 mb-3">Welcome to My Blog</h1>
                        <p class="lead text-muted">A technical blog platform built with Laravel</p>
                        
                        <div class="mt-5">
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-5 me-3">
                                    <i class="bi bi-person-plus me-2"></i>Get Started
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
                                    <i class="bi bi-pencil-square me-2"></i>Write Posts
                                </h5>
                                <p class="card-text text-muted small">Create and manage your technical blog posts with ease.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-success">
                                    <i class="bi bi-chat-dots me-2"></i>Engage
                                </h5>
                                <p class="card-text text-muted small">Interact with readers through comments and discussions.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title text-info">
                                    <i class="bi bi-tags me-2"></i>Organize
                                </h5>
                                <p class="card-text text-muted small">Categorize and tag your content for better discovery.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>