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
                    @if($stats['pending_anonymous'] > 0)
                    <span class="badge bg-danger ms-1">{{ $stats['pending_anonymous'] }}</span>
                    @endif
                </a>
                <a href="{{ route('moderation.index', ['type' => 'posts']) }}"
                    class="btn btn-outline-primary {{ $type === 'posts' ? 'active' : '' }}">
                    Posty
                    @if($stats['pending_posts'] > 0)
                    <span class="badge bg-danger ms-1">{{ $stats['pending_posts'] }}</span>
                    @endif
                </a>
                <a href="{{ route('moderation.index', ['type' => 'reports']) }}"
                    class="btn btn-outline-warning {{ $type === 'reports' ? 'active' : '' }}">
                    Zgłoszenia
                    @if($stats['pending_reports'] > 0)
                    <span class="badge bg-danger ms-1">{{ $stats['pending_reports'] }}</span>
                    @endif
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if($items->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-inbox display-1 text-muted"></i>
            <h3 class="mt-3">Kolejka pusta</h3>
            <p class="text-muted">Brak treści oczekujących na moderację.</p>
        </div>
        @else
        <div class="row">
            @foreach($items as $item)
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

                        @if($type === 'anonymous')
                        <p class="text-muted small mb-2">
                            <i class="bi bi-incognito me-1"></i>
                            Post anonimowy
                            @if($item->fingerprint)
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
                            <button type="submit"
                                class="btn btn-sm btn-success"
                                onclick="return confirm('Zaakceptować ten post?')">
                                <i class="bi bi-check-circle me-1"></i>Akceptuj
                            </button>
                        </form>
                        <a href="{{ route('moderation.show', ['type' => $type, 'id' => $item->id]) }}#reject"
                            class="btn btn-sm btn-danger">
                            <i class="bi bi-x-circle me-1"></i>Odrzuć
                        </a>
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
