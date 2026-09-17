@php
    $isOwn = auth()->check() && auth()->id() === $comment->user_id;
    $isModerator = auth()->check() && auth()->user()->isModerator();
    $isEditable = auth()->check() && $comment->isEditableBy(auth()->user());
@endphp

<div class="card mb-3 {{ $comment->moderation_status === 'pending' ? 'border-warning' : '' }}">
    <div class="card-body">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
                <strong>
                    <i class="bi bi-person-circle me-1"></i>
                    {{ $comment->user->name }}
                </strong>

                @if ($isOwn)
                    <span class="badge bg-secondary ms-1">Ty</span>
                @endif

                <small class="text-muted ms-2">
                    {{ $comment->created_at->diffForHumans() }}
                    @if ($comment->created_at->ne($comment->updated_at))
                        <span class="text-muted">(edytowany)</span>
                    @endif
                </small>
            </div>

            <!-- Status moderacji (widoczny dla autora i moderatorów) -->
            @if ($isOwn || $isModerator)
                @if ($comment->moderation_status === 'pending')
                    <span class="badge bg-warning text-dark">
                        <i class="bi bi-clock me-1"></i>Oczekuje
                    </span>
                @elseif($comment->moderation_status === 'rejected')
                    <span class="badge bg-danger">
                        <i class="bi bi-x-circle me-1"></i>Odrzucony
                    </span>
                @elseif($comment->moderation_status === 'approved')
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>Zaakceptowany
                    </span>
                @endif
            @endif
        </div>

        <!-- Content -->
        <div class="mb-2" style="white-space: pre-wrap;">{{ $comment->content }}</div>

        <!-- Actions -->
        <div class="d-flex gap-3 small">
            @auth
                @if ($comment->canHaveReplies())
                    <a href="#" class="text-decoration-none reply-toggle" data-comment-id="{{ $comment->id }}">
                        <i class="bi bi-reply me-1"></i>Odpowiedz
                    </a>
                @else
                    <span class="text-muted small">
                        <i class="bi bi-info-circle me-1"></i>Maksymalny poziom zagnieżdżenia
                    </span>
                @endif
            @endauth

            @if ($isEditable)
                <a href="{{ route('comments.edit', $comment) }}" class="text-decoration-none">
                    <i class="bi bi-pencil me-1"></i>Edytuj
                </a>
            @endif

            @if ($isOwn || $isModerator || (auth()->check() && auth()->user()->isAdmin()))
                <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="d-inline"
                    onsubmit="return confirm('Usunąć ten komentarz?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link p-0 text-danger text-decoration-none small">
                        <i class="bi bi-trash me-1"></i>Usuń
                    </button>
                </form>
            @endif
        </div>

        <!-- Reply form (ukryty) -->
        @auth
            <div class="reply-form mt-3 d-none" id="reply-form-{{ $comment->id }}">
                <form method="POST" action="{{ route('comments.store', $post) }}">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">

                    <div class="mb-2">
                        <textarea name="content" class="form-control form-control-sm" rows="2" maxlength="2000"
                            placeholder="Odpowiedz {{ $comment->user->name }}..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-send me-1"></i>Odpowiedz
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary reply-cancel"
                        data-comment-id="{{ $comment->id }}">
                        Anuluj
                    </button>
                </form>
            </div>
        @endauth
    </div>

    <!-- Replies (zagnieżdżone) -->
    @if ($comment->replies->count() > 0)
        <div class="card-footer bg-light">
            <div class="ps-4 border-start border-3">
                @foreach ($comment->replies as $reply)
                    @include('comments._comment', ['comment' => $reply, 'post' => $post])
                @endforeach
            </div>
        </div>
    @endif
</div>
