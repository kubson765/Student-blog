<x-layout.app title="Panel administratora">
    <div class="container-fluid py-4">
        <div class="container">

            {{-- ============================================ --}}
            {{-- HEADER --}}
            {{-- ============================================ --}}
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="bi bi-speedometer2 text-primary me-2"></i>Panel administratora
                    </h1>
                    <p class="text-muted mb-0">Przegląd systemu i statystyk</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('moderation.index') }}" class="btn btn-outline-warning">
                        <i class="bi bi-shield-check me-1"></i>Moderacja
                    </a>
                    <a href="{{ route('moderation.reports') }}" class="btn btn-outline-danger">
                        <i class="bi bi-flag me-1"></i>Zgłoszenia
                    </a>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- KARTY STATYSTYK --}}
            {{-- ============================================ --}}
            <div class="row g-3 mb-4">
                {{-- Users --}}
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm h-100 border-primary">
                        <div class="card-body text-center">
                            <i class="bi bi-people-fill fs-2 text-primary"></i>
                            <h3 class="mt-2 mb-0">{{ number_format($stats['users']['total']) }}</h3>
                            <small class="text-muted text-uppercase">Użytkownicy</small>
                            <div class="mt-2 small text-muted">
                                <span class="text-success">
                                    <i class="bi bi-check-circle"></i>
                                    {{ $stats['users']['verified'] }} zweryfikowanych
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Posts --}}
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm h-100 border-success">
                        <div class="card-body text-center">
                            <i class="bi bi-file-post fs-2 text-success"></i>
                            <h3 class="mt-2 mb-0">{{ number_format($stats['posts']['total']) }}</h3>
                            <small class="text-muted text-uppercase">Posty</small>
                            <div class="mt-2 small text-muted">
                                <span class="text-success">
                                    <i class="bi bi-check-circle"></i>
                                    {{ $stats['posts']['published'] }} opublikowanych
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Comments --}}
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm h-100 border-info">
                        <div class="card-body text-center">
                            <i class="bi bi-chat-dots-fill fs-2 text-info"></i>
                            <h3 class="mt-2 mb-0">{{ number_format($stats['comments']['total']) }}</h3>
                            <small class="text-muted text-uppercase">Komentarze</small>
                            <div class="mt-2 small text-muted">
                                <span class="text-success">
                                    <i class="bi bi-check-circle"></i>
                                    {{ $stats['comments']['approved'] }} zaakceptowanych
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Reports --}}
                <div class="col-6 col-md-3">
                    <div class="card shadow-sm h-100 border-danger">
                        <div class="card-body text-center">
                            <i class="bi bi-flag-fill fs-2 text-danger"></i>
                            <h3 class="mt-2 mb-0">{{ number_format($stats['reports']['total']) }}</h3>
                            <small class="text-muted text-uppercase">Zgłoszenia</small>
                            <div class="mt-2 small text-muted">
                                @if ($stats['reports']['pending'] > 0)
                                    <span class="text-danger">
                                        <i class="bi bi-exclamation-triangle"></i>
                                        {{ $stats['reports']['pending'] }} oczekuje
                                    </span>
                                @else
                                    <span class="text-success">
                                        <i class="bi bi-check-circle"></i>
                                        Brak oczekujących
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- KOLEJKA MODERACJI --}}
            {{-- ============================================ --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-hourglass-split me-2"></i>Kolejka moderacji
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <a href="{{ route('moderation.index', ['type' => 'anonymous']) }}"
                                class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-incognito fs-2 text-secondary me-3"></i>
                                    <div>
                                        <h4 class="mb-0 text-dark">{{ $moderationQueue['anonymous'] }}</h4>
                                        <small class="text-muted">Anonimowe posty</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('moderation.index', ['type' => 'posts']) }}"
                                class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-file-post fs-2 text-primary me-3"></i>
                                    <div>
                                        <h4 class="mb-0 text-dark">{{ $moderationQueue['posts'] }}</h4>
                                        <small class="text-muted">Posty</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('moderation.index', ['type' => 'comments']) }}"
                                class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-chat-dots fs-2 text-info me-3"></i>
                                    <div>
                                        <h4 class="mb-0 text-dark">{{ $moderationQueue['comments'] }}</h4>
                                        <small class="text-muted">Komentarze</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-6 col-md-3">
                            <a href="{{ route('moderation.reports') }}" class="text-decoration-none">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-flag fs-2 text-danger me-3"></i>
                                    <div>
                                        <h4 class="mb-0 text-dark">{{ $moderationQueue['reports'] }}</h4>
                                        <small class="text-muted">Zgłoszenia</small>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- SZCZEGÓŁY UŻYTKOWNIKÓW + TREŚCI --}}
            {{-- ============================================ --}}
            <div class="row g-3 mb-4">
                {{-- Users detail --}}
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-people me-2"></i>Użytkownicy – szczegóły
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td>Wszyscy</td>
                                        <td class="text-end fw-bold">{{ number_format($stats['users']['total']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Zweryfikowani</td>
                                        <td class="text-end">{{ number_format($stats['users']['verified']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Moderatorzy</td>
                                        <td class="text-end text-warning">
                                            {{ number_format($stats['users']['moderators']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Administratorzy</td>
                                        <td class="text-end text-danger">{{ number_format($stats['users']['admins']) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Zbanowani</td>
                                        <td class="text-end text-danger">{{ number_format($stats['users']['banned']) }}
                                        </td>
                                    </tr>
                                    <tr class="table-light">
                                        <td>Nowi (7 dni)</td>
                                        <td class="text-end fw-bold text-success">
                                            +{{ number_format($stats['users']['new_last_7_days']) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Content detail --}}
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-file-post me-2"></i>Treści – szczegóły
                            </h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr>
                                        <td>Posty opublikowane</td>
                                        <td class="text-end text-success fw-bold">
                                            {{ number_format($stats['posts']['published']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Szkice</td>
                                        <td class="text-end text-warning">
                                            {{ number_format($stats['posts']['drafts']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Anonimowe posty</td>
                                        <td class="text-end">{{ number_format($stats['anonymous_posts']['total']) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Anonimowe oczekujące</td>
                                        <td class="text-end text-warning">
                                            {{ number_format($stats['anonymous_posts']['pending']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Komentarze</td>
                                        <td class="text-end">{{ number_format($stats['comments']['total']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Komentarze oczekujące</td>
                                        <td class="text-end text-warning">
                                            {{ number_format($stats['comments']['pending']) }}</td>
                                    </tr>
                                    <tr class="table-light">
                                        <td>Interakcje</td>
                                        <td class="text-end fw-bold">
                                            {{ number_format($stats['interactions']['total']) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- OSTATNIE ZGŁOSZENIA --}}
            {{-- ============================================ --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="bi bi-flag me-2"></i>Ostatnie zgłoszenia
                    </h6>
                    <a href="{{ route('moderation.reports') }}" class="btn btn-sm btn-light">
                        Zobacz wszystkie
                    </a>
                </div>
                @if ($recentReports->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Powód</th>
                                    <th>Zgłaszający</th>
                                    <th>Treść</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentReports as $report)
                                    <tr>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ str_replace('_', ' ', $report->reason) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($report->reporter)
                                                {{ $report->reporter->name }}
                                            @else
                                                <span class="fst-italic text-muted">Anonimowy</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($report->reportable)
                                                <span class="badge bg-secondary me-1">
                                                    {{ class_basename($report->reportable_type) }}
                                                </span>
                                                {{ Str::limit($report->reportable->title ?? 'Komentarz', 40) }}
                                            @else
                                                <span class="text-muted">Usunięte</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">
                                            {{ $report->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-center text-muted py-4">
                        <i class="bi bi-shield-check display-4 text-success"></i>
                        <p class="mt-2 mb-0">Brak oczekujących zgłoszeń</p>
                    </div>
                @endif
            </div>

            {{-- ============================================ --}}
            {{-- HISTORIA MODERACJI --}}
            {{-- ============================================ --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Ostatnie akcje moderacji
                    </h6>
                </div>
                @if ($recentModerationActions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Moderator</th>
                                    <th>Akcja</th>
                                    <th>Treść</th>
                                    <th>Powód</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recentModerationActions as $action)
                                    <tr>
                                        <td>{{ $action->moderator?->name ?? '–' }}</td>
                                        <td>
                                            @php
                                                $actionBadge = match ($action->action) {
                                                    'approve' => 'bg-success',
                                                    'reject' => 'bg-danger',
                                                    'spam' => 'bg-secondary',
                                                    'escalate' => 'bg-warning text-dark',
                                                    default => 'bg-light text-dark',
                                                };
                                            @endphp
                                            <span class="badge {{ $actionBadge }}">
                                                {{ $action->action }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($action->moderatable)
                                                <span class="badge bg-secondary me-1">
                                                    {{ class_basename($action->moderatable_type) }}
                                                </span>
                                                {{ Str::limit($action->moderatable->title ?? 'Komentarz', 30) }}
                                            @else
                                                <span class="text-muted">Usunięte</span>
                                            @endif
                                        </td>
                                        <td class="small text-muted">
                                            {{ Str::limit($action->reason, 50) }}
                                        </td>
                                        <td class="text-muted small">
                                            {{ $action->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body text-center text-muted py-4">
                        <p class="mb-0">Brak akcji moderacji</p>
                    </div>
                @endif
            </div>

            {{-- ============================================ --}}
            {{-- OSTATNI UŻYTKOWNICY + TOP TAGI --}}
            {{-- ============================================ --}}
            <div class="row g-3">
                {{-- Recent users --}}
                <div class="col-md-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="bi bi-person-plus me-2"></i>Ostatni użytkownicy
                            </h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Imię</th>
                                        <th>Email</th>
                                        <th>Rola</th>
                                        <th>Dołączył</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentUsers as $user)
                                        <tr>
                                            <td>
                                                {{ $user->name }}
                                                @if ($user->isBanned())
                                                    <span class="badge bg-danger ms-1">Ban</span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">{{ $user->email }}</td>
                                            <td>
                                                @if ($user->role === 'admin')
                                                    <span class="badge bg-danger">admin</span>
                                                @elseif($user->role === 'moderator')
                                                    <span class="badge bg-warning text-dark">moderator</span>
                                                @else
                                                    <span class="badge bg-light text-dark">user</span>
                                                @endif
                                            </td>
                                            <td class="small text-muted">
                                                {{ $user->created_at->diffForHumans() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Top tags --}}
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-info text-dark">
                            <h6 class="mb-0">
                                <i class="bi bi-tags me-2"></i>Top 5 tagów
                            </h6>
                        </div>
                        <div class="card-body">
                            @forelse($topTags as $tag)
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2
                                    @if (!$loop->last) pb-2 border-bottom @endif">
                                    <div>
                                        <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                                            class="text-decoration-none">
                                            <span class="badge bg-secondary me-2">#</span>
                                            {{ $tag->name }}
                                        </a>
                                    </div>
                                    <span class="badge bg-light text-dark">
                                        {{ $tag->posts_count }} {{ $tag->posts_count === 1 ? 'post' : 'postów' }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-muted text-center mb-0">Brak tagów</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layout.app>
