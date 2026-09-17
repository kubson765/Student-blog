<x-layout.app title="Zgłoszenia – Moderacja">
    <div class="container py-4">

        {{-- Header --}}
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-flag text-danger me-2"></i>Zgłoszenia
                </h1>
                <p class="text-muted mb-0">Zgłoszenia treści przez użytkowników</p>
            </div>
            <a href="{{ route('moderation.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Wróć do kolejki
            </a>
        </div>

        {{-- Statystyki --}}
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="bi bi-clock-history fs-3 text-warning"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['pending'] }}</h4>
                        <small class="text-muted">Oczekujące</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="bi bi-check-circle fs-3 text-success"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['resolved'] }}</h4>
                        <small class="text-muted">Rozpatrzone</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-secondary">
                    <div class="card-body text-center">
                        <i class="bi bi-x-circle fs-3 text-secondary"></i>
                        <h4 class="mt-2 mb-0">{{ $stats['dismissed'] }}</h4>
                        <small class="text-muted">Odrzucone</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filtry --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('moderation.reports') }}">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="status"
                                class="form-label small fw-bold text-muted text-uppercase">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">Wszystkie</option>
                                <option value="pending" @selected(request('status') === 'pending')>Oczekujące</option>
                                <option value="reviewing" @selected(request('status') === 'reviewing')>W trakcie</option>
                                <option value="resolved" @selected(request('status') === 'resolved')>Rozpatrzone</option>
                                <option value="dismissed" @selected(request('status') === 'dismissed')>Odrzucone</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="reason"
                                class="form-label small fw-bold text-muted text-uppercase">Powód</label>
                            <select name="reason" id="reason" class="form-select">
                                <option value="">Wszystkie</option>
                                <option value="spam" @selected(request('reason') === 'spam')>Spam</option>
                                <option value="harassment" @selected(request('reason') === 'harassment')>Nękanie</option>
                                <option value="hate_speech" @selected(request('reason') === 'hate_speech')>Mowa nienawiści</option>
                                <option value="sexual_content" @selected(request('reason') === 'sexual_content')>Treści seksualne</option>
                                <option value="violence" @selected(request('reason') === 'violence')>Przemoc</option>
                                <option value="misinformation" @selected(request('reason') === 'misinformation')>Dezinformacja</option>
                                <option value="copyright" @selected(request('reason') === 'copyright')>Prawa autorskie</option>
                                <option value="other" @selected(request('reason') === 'other')>Inne</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-funnel me-1"></i>Filtruj
                            </button>
                            <a href="{{ route('moderation.reports') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-1"></i>Wyczyść
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Lista zgłoszeń --}}
        @if ($reports->count() > 0)
            <div class="row g-3">
                @foreach ($reports as $report)
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div
                                class="card-header d-flex justify-content-between align-items-center
                                @if ($report->status === 'pending') bg-warning text-dark
                                @elseif($report->status === 'resolved') bg-success text-white
                                @elseif($report->status === 'dismissed') bg-secondary text-white
                                @else bg-info text-dark @endif">
                                <div>
                                    <span class="badge bg-light text-dark me-2">{{ $report->status }}</span>
                                    <strong>{{ str_replace('_', ' ', $report->reason) }}</strong>
                                </div>
                                <small>{{ $report->created_at->diffForHumans() }}</small>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    {{-- Zgłaszający --}}
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <h6 class="text-muted small text-uppercase">Zgłaszający</h6>
                                        @if ($report->reporter)
                                            <i class="bi bi-person-circle me-1"></i>
                                            {{ $report->reporter->name }}
                                        @else
                                            <i class="bi bi-incognito me-1"></i>
                                            <span class="fst-italic">Anonimowy</span>
                                        @endif
                                        <div class="small text-muted mt-1">
                                            <code>FP:
                                                {{ substr($report->reporter_fingerprint ?? '', 0, 12) }}...</code>
                                        </div>
                                    </div>

                                    {{-- Zgłoszona treść --}}
                                    <div class="col-md-8">
                                        <h6 class="text-muted small text-uppercase">Zgłoszona treść</h6>
                                        @php
                                            $reportable = $report->reportable;
                                            $type = class_basename($report->reportable_type);
                                        @endphp

                                        @if ($reportable)
                                            <div class="border rounded p-2 bg-light">
                                                <div class="d-flex justify-content-between align-items-start mb-1">
                                                    <span class="badge bg-secondary">{{ $type }}</span>
                                                    <small class="text-muted">ID: {{ $reportable->id }}</small>
                                                </div>
                                                <p class="mb-1 fw-bold">
                                                    {{ Str::limit($reportable->title ?? 'Komentarz', 80) }}</p>
                                                <p class="mb-0 small text-muted">
                                                    {{ Str::limit($reportable->content, 150) }}</p>
                                            </div>
                                        @else
                                            <p class="text-muted">Treść została usunięta.</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Opis zgłoszenia --}}
                                @if ($report->description)
                                    <div class="mt-3">
                                        <h6 class="text-muted small text-uppercase">Opis od zgłaszającego</h6>
                                        <p class="mb-0 small">{{ $report->description }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Akcje (tylko dla pending) --}}
                            @if ($report->status === 'pending')
                                <div class="card-footer bg-white d-flex gap-2">
                                    <button type="button" class="btn btn-danger flex-grow-1" data-bs-toggle="modal"
                                        data-bs-target="#resolveModal-{{ $report->id }}">
                                        <i class="bi bi-check-circle me-1"></i>Rozpatrz (ukryj treść)
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary flex-grow-1"
                                        data-bs-toggle="modal" data-bs-target="#dismissModal-{{ $report->id }}">
                                        <i class="bi bi-x-circle me-1"></i>Odrzuć zgłoszenie
                                    </button>
                                </div>

                                {{-- Resolve Modal --}}
                                <div class="modal fade" id="resolveModal-{{ $report->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('moderation.reports.resolve', $report) }}">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title">Rozpatrz zgłoszenie</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted">
                                                        Treść zostanie ukryta. Wszystkie inne zgłoszenia dla tej treści
                                                        zostaną automatycznie zamknięte.
                                                    </p>
                                                    <label for="reason-{{ $report->id }}"
                                                        class="form-label small fw-bold">
                                                        Uzasadnienie (min. 10 znaków)
                                                    </label>
                                                    <textarea name="reason" id="reason-{{ $report->id }}" class="form-control" rows="3" minlength="10"
                                                        maxlength="500" required placeholder="Np. Treść narusza regulamin – spam"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Anuluj</button>
                                                    <button type="submit" class="btn btn-danger">
                                                        <i class="bi bi-check-circle me-1"></i>Rozpatrz
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Dismiss Modal --}}
                                <div class="modal fade" id="dismissModal-{{ $report->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST"
                                                action="{{ route('moderation.reports.dismiss', $report) }}">
                                                @csrf
                                                <div class="modal-header bg-secondary text-white">
                                                    <h5 class="modal-title">Odrzuć zgłoszenie</h5>
                                                    <button type="button" class="btn-close btn-close-white"
                                                        data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="small text-muted">
                                                        Treść pozostanie opublikowana. Zgłoszenie zostanie oznaczone
                                                        jako odrzucone.
                                                    </p>
                                                    <label for="dismiss-reason-{{ $report->id }}"
                                                        class="form-label small fw-bold">
                                                        Uzasadnienie (min. 10 znaków)
                                                    </label>
                                                    <textarea name="reason" id="dismiss-reason-{{ $report->id }}" class="form-control" rows="3"
                                                        minlength="10" maxlength="500" required placeholder="Np. Treść nie narusza regulaminu"></textarea>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Anuluj</button>
                                                    <button type="submit" class="btn btn-secondary">
                                                        <i class="bi bi-x-circle me-1"></i>Odrzuć zgłoszenie
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginacja --}}
            <div class="d-flex justify-content-center mt-4">
                {{ $reports->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-shield-check display-1 text-success"></i>
                <h3 class="mt-3">Brak zgłoszeń</h3>
                <p class="text-muted">
                    @if (request()->hasAny(['status', 'reason']))
                        Brak zgłoszeń spełniających kryteria.
                    @else
                        Wszystko w porządku – brak zgłoszeń do rozpatrzenia.
                    @endif
                </p>
            </div>
        @endif

    </div>
</x-layout.app>
