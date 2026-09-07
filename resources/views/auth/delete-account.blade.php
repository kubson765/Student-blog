<x-layout.app title="Delete Account">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>Delete Account
                        </h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="alert alert-danger">
                            <h5 class="alert-heading">
                                <i class="bi bi-exclamation-circle me-2"></i>Warning!
                            </h5>
                            <p>This action <strong>cannot be undone</strong>. This will permanently delete:</p>
                            <ul>
                                <li>Your account and profile information</li>
                                <li>All your posts (they will be anonymized)</li>
                                <li>All your comments (they will be anonymized)</li>
                                <li>All your interactions and bookmarks</li>
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('account.destroy') }}">
                            @csrf
                            @method('DELETE')

                            <!-- Password -->
                            <div class="mb-3">
                                <label for="password" class="form-label">Enter Your Password</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-lock"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           required>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    Enter your password to confirm account deletion.
                                </div>
                            </div>

                            <!-- Confirmation Checkbox -->
                            <div class="mb-3 form-check">
                                <input type="checkbox" 
                                       class="form-check-input @error('confirmation') is-invalid @enderror" 
                                       id="confirmation" 
                                       name="confirmation" 
                                       value="1" 
                                       required>
                                <label class="form-check-label" for="confirmation">
                                    I understand that this action is <strong>permanent</strong> and cannot be undone.
                                </label>
                                @error('confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash me-2"></i>Permanently Delete Account
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>