<x-layout.app title="Edytuj komentarz">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil me-2"></i>Edytuj komentarz
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('comments.update', $comment) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="content" class="form-label">Treść</label>
                                <textarea name="content" id="content" class="form-control @error('content') is-invalid @enderror" rows="5"
                                    maxlength="2000" required>{{ old('content', $comment->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Po edycji komentarz wróci do moderacji.
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-2"></i>Zapisz
                                </button>
                                <a href="{{ route('posts.show', $comment->post) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-left me-2"></i>Anuluj
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
