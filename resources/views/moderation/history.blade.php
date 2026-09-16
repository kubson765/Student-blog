{{-- resources/views/moderation/history.blade.php --}}
<x-layout.app title="Historia moderacji">
    <div class="container py-4">
        <h1 class="h4 mb-4">
            <i class="bi bi-clock-history me-2"></i>Historia moderacji
        </h1>

        <div class="card shadow-sm">
            <div class="card-body">
                @forelse($actions as $action)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span
                                    class="badge bg-{{ $action->action === 'approve' ? 'success' : ($action->action === 'reject' ? 'danger' : 'secondary') }}">
                                    {{ $action->action }}
                                </span>
                                <strong class="ms-2">{{ $action->moderator->name }}</strong>
                                <small class="text-muted">{{ $action->created_at->format('Y-m-d H:i') }}</small>
                            </div>
                        </div>
                        <p class="mt-2 mb-0"><strong>Powód:</strong> {{ $action->reason }}</p>
                        @if ($action->context)
                            <small class="text-muted">
                                Kontekst: <code>{{ json_encode($action->context) }}</code>
                            </small>
                        @endif
                    </div>
                @empty
                    <p class="text-muted text-center py-3">Brak historii moderacji.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layout.app>
