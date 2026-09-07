<x-layout.app title="Dashboard">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Dashboard</h4>
                        <span class="badge bg-light text-dark">
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            Verified
                        </span>
                    </div>
                    <div class="card-body">
                        <h5>Welcome, {{ auth()->user()->name }}! 👋</h5>
                        <p class="text-muted">Your email address <strong>{{ auth()->user()->email }}</strong> has been verified.</p>
                        
                        <hr>
                        
                        <div class="row mt-4">
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-file-post fs-1 text-primary"></i>
                                        <h6 class="mt-2">Your Posts</h6>
                                        <p class="small text-muted">0 posts</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-chat fs-1 text-info"></i>
                                        <h6 class="mt-2">Comments</h6>
                                        <p class="small text-muted">0 comments</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-heart fs-1 text-danger"></i>
                                        <h6 class="mt-2">Interactions</h6>
                                        <p class="small text-muted">0 likes</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Przyciski akcji -->
                        <div class="mt-4 d-flex flex-wrap gap-3">
                            <a href="{{ route('password.change') }}" class="btn btn-outline-warning">
                                <i class="bi bi-key me-2"></i>Change Password
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>