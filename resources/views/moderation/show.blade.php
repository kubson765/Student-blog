<x-layout.app title="Moderacja: {{ $post->title }}">
    <div class="container py-4">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('moderation.index', ['type' => $type]) }}">Moderacja</a>
                </li>
                <li class="breadcrumb-item active">Szczegóły</li>
            </ol>
        </nav>

        <div class="row">
            {{-- Lewa kolumna: treść --}}
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            <i class="bi bi-file-text me-2"></i>{{ $post->title }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if ($type === 'comments')
                            <div class="mb-3">
                                <small class="text-muted">Komentarz do posta:</small>
                                <a href="{{ route('posts.show', $post->post) }}" target="_blank">
                                    {{ $post->post->title }}
                                    <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        @endif
                        <div class="mb-3 text-muted small">
                            <i class="bi bi-clock me-1"></i>
                            Utworzono: {{ $post->created_at->format('Y-m-d H:i:s') }}
                            @if ($post->fingerprint)
                                | <i class="bi bi-fingerprint me-1"></i>
                                Fingerprint: <code>{{ substr($post->fingerprint, 0, 16) }}...</code>
                            @else
                                <i class="bi bi-person-circle me-1"></i>
                                Autor: {{ $post->user?->name ?? 'Nieznany' }}
                            @endif
                        </div>

                        <div class="post-content" style="white-space: pre-wrap; line-height: 1.7;">
                            {{ $post->content }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prawa kolumna: akcje --}}
            <div class="col-md-4">
                {{-- Akceptuj --}}
                <div class="card shadow-sm mb-3 border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-check-circle me-2"></i>Akceptuj</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('moderation.approve', ['type' => $type, 'id' => $post->id]) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small">Powód akceptacji</label>
                                <textarea name="reason" class="form-control form-control-sm" rows="3" minlength="0" maxlength="500" required
                                    placeholder="Np. Treść zgodna z regulaminem, wartościowa wypowiedź"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-check-circle me-1"></i>Zaakceptuj i opublikuj
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Odrzuć --}}
                <div class="card shadow-sm mb-3 border-danger" id="reject">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="bi bi-x-circle me-2"></i>Odrzuć</h6>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('moderation.reject', ['type' => $type, 'id' => $post->id]) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small">Powód odrzucenia</label>
                                <textarea name="reason" class="form-control form-control-sm" rows="3" minlength="10" maxlength="500" required
                                    placeholder="Np. Treść zawiera wulgaryzmy / spam / narusza regulamin"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('Odrzucić ten post? Autor nie zostanie powiadomiony.')">
                                <i class="bi bi-x-circle me-1"></i>Odrzuć
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Eskaluj --}}
                {{-- <div class="card shadow-sm mb-3 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0"><i class="bi bi-arrow-up-circle me-2"></i>Eskaluj do admina</h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-2">
                            Użyj, gdy nie jesteś pewien lub sprawa wymaga wyższej decyzji.
                        </p>
                        <form method="POST"
                            action="{{ route('moderation.escalate', ['type' => $type, 'id' => $post->id]) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small">Powód eskalacji</label>
                                <textarea name="reason" class="form-control form-control-sm" rows="3" minlength="10" maxlength="500" required
                                    placeholder="Np. Post może naruszać prawo autorskie – wymaga decyzji admina"></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-arrow-up-circle me-1"></i>Eskaluj
                            </button>
                        </form>
                    </div>
                </div> --}}
                <div class="mt-3">
                    <a href="{{ route('moderation.history', ['type' => $type, 'id' => $post->id]) }}"
                        class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-clock-history me-1"></i>Historia moderacji
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
