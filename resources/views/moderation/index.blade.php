{{-- resources/views/moderation/index.blade.php --}}
<x-layout.app title="Panel Moderacji">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-shield-check text-primary me-2"></i>Panel Moderacji
                </h1>
                <p class="text-muted mb-0">Witaj, {{ auth()->user()->name }}</p>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('moderation.index', ['type' => 'anonymous']) }}"
                    class="btn btn-outline-primary {{ $type === 'anonymous' ? 'active' : '' }}">
                    Anonimowe
                    @if ($stats['pending_anonymous'] > 0)
                        <span class="badge bg-danger ms-1">{{ $stats['pending_anonymous'] }}</span>
                    @endif
                </a>
                <a href="{{ route('moderation.index', ['type' => 'posts']) }}"
                    class="btn btn-outline-primary {{ $type === 'posts' ? 'active' : '' }}">
                    Posty
                    @if ($stats['pending_posts'] > 0)
                        <span class="badge bg-danger ms-1">{{ $stats['pending_posts'] }}</span>
                    @endif
                </a>
                <a href="{{ route('moderation.index', ['type' => 'comments']) }}"
                    class="btn btn-outline-primary {{ $type === 'comments' ? 'active' : '' }}">
                    Komentarze
                    @if (($stats['pending_comments'] ?? 0) > 0)
                        <span class="badge bg-danger ms-1">{{ $stats['pending_comments'] }}</span>
                    @endif
                </a>
                <a href="{{ route('moderation.reports', ['type' => 'reports']) }}"
                    class="btn btn-outline-warning {{ $type === 'reports' ? 'active' : '' }}">
                    Zgłoszenia
                    @if ($stats['pending_reports'] > 0)
                        <span class="badge bg-danger ms-1">{{ $stats['pending_reports'] }}</span>
                    @endif
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($items->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox display-1 text-muted"></i>
                <h3 class="mt-3">Kolejka pusta</h3>
                <p class="text-muted">Brak treści oczekujących na moderację.</p>
            </div>
        @else
            <div class="row">
                @foreach ($items as $item)
                    @if ($type === 'comments')
                        <p class="text-muted small mb-2">
                            <i class="bi bi-chat me-1"></i>
                            Komentarz do:
                            <a href="{{ route('posts.show', $item->post) }}">
                                {{ $item->post->title }}
                            </a>
                        </p>
                        <p class="card-text">{{ Str::limit($item->content, 100) }}</p>
                    @else
                        <h5 class="card-title">{{ $item->title }}</h5>
                        <p class="card-text">{{ Str::limit($item->content, 100) }}</p>
                    @endif
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i>Oczekuje
                                </span>
                                <small class="text-muted">
                                    {{ $item->created_at->diffForHumans() }}
                                </small>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title">{{ $item->title }}</h5>

                                @if ($type === 'anonymous')
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-incognito me-1"></i>
                                        Post anonimowy
                                        @if ($item->fingerprint)
                                            <span class="ms-2 badge bg-secondary">
                                                FP: {{ substr($item->fingerprint, 0, 8) }}
                                            </span>
                                        @endif
                                    </p>
                                @endif

                                <p class="card-text">
                                    {{ Str::limit($item->content, 200) }}
                                </p>
                            </div>
                            <div class="card-footer bg-white d-flex gap-2">
                                <a href="{{ route('moderation.show', ['type' => $type, 'id' => $item->id]) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye me-1"></i>Zobacz pełny
                                </a>
                                <form method="POST"
                                    action="{{ route('moderation.approve', ['type' => $type, 'id' => $item->id]) }}"
                                    class="d-inline">
                                    @csrf
                                    <input type="hidden" name="reason" value="Zaakceptowano z panelu moderacji">
                                    <button type="submit" class="btn btn-sm btn-success"
                                        onclick="return confirm('Zaakceptować?')">
                                        <i class="bi bi-check-circle me-1"></i>Akceptuj
                                    </button>
                                </form>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#rejectModal-{{ $item->id }}">
                                    <i class="bi bi-x-circle me-1"></i>Odrzuć
                                </button>

                                <div class="modal fade" id="rejectModal-{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('moderation.reject', ['type' => $type, 'id' => $item->id]) }}">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Odrzuć treść</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted">
                                                        Podaj powód odrzucenia (będzie zapisany w historii moderacji).
                                                    </p>
                                                    <textarea name="reason" class="form-control" rows="3" minlength="10" maxlength="500" required
                                                        placeholder="Np. Treść narusza regulamin – spam"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Anuluj</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-x-circle me-1"></i>Odrzuć
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</x-layout.app>
