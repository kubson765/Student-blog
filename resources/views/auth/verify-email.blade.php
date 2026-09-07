<x-layout.app title="Verify Email">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Verify Your Email Address</h3>
                    </div>
                    <div class="card-body">
                        @if (session('status') === 'verification-link-sent')
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                A new verification link has been sent to your email address.
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <p class="card-text mb-4">
                            Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we'll gladly send you another. Note that your account will be removed due to security reasons if you fail to verify it.
                        </p>

                        <div class="d-flex flex-wrap gap-3">
                            <!-- Resend Verification -->
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-envelope-paper me-2"></i>Resend Verification Email
                                </button>
                            </form>

                            <!-- Logout -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary">
                                    <i class="bi bi-box-arrow-right me-2"></i>Logout
                                </button>
                            </form>
                        </div>
                        
                        <div class="mt-4 pt-3 border-top text-muted small">
                            <i class="bi bi-info-circle me-1"></i>
                            Verification email sent to: <strong>{{ auth()->user()->email ?? 'your email' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>