<div class="comments-section mt-5">
    <h5 class="mb-4">
        <i class="bi bi-chat-dots me-2"></i>
        Komentarze ({{ $post->comments()->approved()->count() }})
    </h5>

    @auth
        <div class="card mb-4">
            <div class="card-body">
                <form method="POST" action="{{ route('comments.store', $post) }}">
                    @csrf

                    <div class="mb-3">
                        <textarea name="content" class="form-control @error('content') is-invalid @enderror" rows="3" maxlength="2000"
                            placeholder="Napisz komentarz..." required>{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-2"></i>Dodaj komentarz
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>
            <a href="{{ route('login') }}">Zaloguj się</a>, aby dodać komentarz.
        </div>
    @endauth

    @forelse($comments as $comment)
        @include('comments._comment', ['comment' => $comment, 'post' => $post])
    @empty
        <div class="text-center py-4 text-muted">
            <i class="bi bi-chat-square-text display-4 d-block mb-2"></i>
            Brak komentarzy. Bądź pierwszy!
        </div>
    @endforelse

    <div class="mt-4">
        {{ $comments->links() }}
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle reply form
            document.querySelectorAll('.reply-toggle').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const id = this.dataset.commentId;
                    const form = document.getElementById(`reply-form-${id}`);
                    form.classList.toggle('d-none');
                });
            });

            // Cancel reply
            document.querySelectorAll('.reply-cancel').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.commentId;
                    const form = document.getElementById(`reply-form-${id}`);
                    form.classList.add('d-none');
                });
            });
        });
    </script>
@endpush
