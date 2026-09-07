<x-layout.app title="Dashboard">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-speedometer2 me-2"></i>Dashboard
                        </h4>
                        @if(auth()->user()->hasVerifiedEmail())
                            <span class="badge bg-light text-dark">
                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                Verified
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Not Verified
                            </span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <h5>Welcome back, <strong>{{ auth()->user()->name }}</strong>! 👋</h5>
                        <p class="text-muted">
                            <i class="bi bi-envelope me-1"></i>
                            {{ auth()->user()->email }}
                            @if(!auth()->user()->hasVerifiedEmail())
                                <span class="badge bg-warning ms-2">Unverified</span>
                                <a href="{{ route('verification.send') }}" class="text-decoration-none ms-2">
                                    Resend verification
                                </a>
                            @endif
                        </p>
                        
                        <hr>
                        
                        <!-- Account Settings Cards -->
                        <h5 class="mb-3">Account Settings</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-key fs-1 text-warning"></i>
                                        <h6 class="mt-2">Change Password</h6>
                                        <p class="small text-muted">Update your password</p>
                                        <a href="{{ route('password.change') }}" class="btn btn-outline-warning btn-sm">
                                            Change Password
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="bi bi-envelope fs-1 text-info"></i>
                                        <h6 class="mt-2">Change Email</h6>
                                        <p class="small text-muted">Update your email address</p>
                                        <a href="{{ route('email.change') }}" class="btn btn-outline-info btn-sm">
                                            Change Email
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="card h-100 border-danger">
                                    <div class="card-body text-center">
                                        <i class="bi bi-trash fs-1 text-danger"></i>
                                        <h6 class="mt-2 text-danger">Delete Account</h6>
                                        <p class="small text-muted">Permanently delete your account</p>
                                        <a href="{{ route('account.delete') }}" class="btn btn-outline-danger btn-sm">
                                            Delete Account
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <!-- Statistics -->
                        <h5 class="mb-3">Your Activity</h5>
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-file-post fs-1 text-primary"></i>
                                        <h5 class="mt-2">{{ auth()->user()->posts()->count() }}</h5>
                                        <p class="small text-muted mb-0">Posts</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-chat fs-1 text-info"></i>
                                        <h5 class="mt-2">{{ auth()->user()->comments()->count() }}</h5>
                                        <p class="small text-muted mb-0">Comments</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-heart fs-1 text-danger"></i>
                                        <h5 class="mt-2">{{ auth()->user()->interactions()->count() }}</h5>
                                        <p class="small text-muted mb-0">Interactions</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="card bg-light border-0">
                                    <div class="card-body text-center">
                                        <i class="bi bi-calendar fs-1 text-success"></i>
                                        <h5 class="mt-2">{{ auth()->user()->created_at->diffForHumans() }}</h5>
                                        <p class="small text-muted mb-0">Member since</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Logout -->
                        <div class="mt-4">
                            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
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