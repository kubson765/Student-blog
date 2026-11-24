<x-layout.app title="Blog studencki">
    <div class="container py-5">

        {{-- ============================================ --}}
        {{-- HEADER --}}
        {{-- ============================================ --}}
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="display-6 fw-bold mb-1">
                    <i class="bi bi-journal-text text-primary me-2"></i>Blog studencki
                </h1>
                <p class="text-muted mb-0">Odkryj doświadczenia innych studentów</p>
            </div>
            @auth
                @can('create', App\Models\Post::class)
                    <a href="{{ route('posts.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Nowy wpis
                    </a>
                @endcan
            @endauth
        </div>

        {{-- ============================================ --}}
        {{-- ALERTY --}}
        {{-- ============================================ --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- ============================================ --}}
        {{-- FILTRY --}}
        {{-- ============================================ --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('posts.index') }}" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="search" class="form-label small fw-bold text-muted text-uppercase">
                                <i class="bi bi-search me-1"></i>Szukaj
                            </label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ $activeFilters['search'] ?? '' }}" placeholder="Tytuł lub treść...">
                        </div>

                        <div class="col-md-3">
                            <label for="category" class="form-label small fw-bold text-muted text-uppercase">
                                <i class="bi bi-folder me-1"></i>Kategoria
                            </label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Wszystkie</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" @selected(($activeFilters['category'] ?? '') === $cat)>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label small fw-bold text-muted text-uppercase">
                                <i class="bi bi-tag me-1"></i>Tagi
                                @if (!empty($activeFilters['tags']))
                                    <span class="badge bg-primary ms-1">{{ count($activeFilters['tags']) }}</span>
                                @endif
                            </label>
                            <button type="button"
                                class="form-control text-start d-flex justify-content-between align-items-center"
                                id="tagPickerTrigger" data-bs-toggle="modal" data-bs-target="#tagPickerModal">
                                <span>{{ empty($activeFilters['tags']) ? 'Wybierz tagi' : 'Wybrane: ' . count($activeFilters['tags']) }}</span>
                                <i class="bi bi-chevron-down small"></i>
                            </button>
                            <div class="d-flex flex-wrap gap-1 mt-2">
                                @foreach ($activeFilters['tags'] as $tagSlug)
                                    @php $activeTag = $tags->firstWhere('slug', $tagSlug); @endphp
                                    @if ($activeTag)
                                        <span class="badge bg-primary">#{{ $activeTag->name }}</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-2">
                            <label for="sort" class="form-label small fw-bold text-muted text-uppercase">
                                <i class="bi bi-sort-down me-1"></i>Sortuj
                            </label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="popular" @selected(($activeFilters['sort'] ?? '') === 'popular')>Popularne</option>
                                <option value="latest" @selected(($activeFilters['sort'] ?? 'latest') === 'latest')>Najnowsze</option>
                                <option value="oldest" @selected(($activeFilters['sort'] ?? '') === 'oldest')>Najstarsze</option>
                                <option value="title" @selected(($activeFilters['sort'] ?? '') === 'title')>Tytuł (A-Z)</option>
                            </select>
                        </div>
                    </div>

                    @auth
                        <div class="row g-3 mt-1">
                            <div class="col-md-3">
                                <label for="status" class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="bi bi-funnel me-1"></i>Status
                                </label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Wszystkie widoczne</option>
                                    <option value="published" @selected(($activeFilters['status'] ?? '') === 'published')>Opublikowane</option>
                                    <option value="draft" @selected(($activeFilters['status'] ?? '') === 'draft')>Moje szkice</option>
                                </select>
                            </div>
                        </div>
                    @endauth

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-funnel me-2"></i>Zastosuj filtry
                        </button>
                        @if (!empty(array_filter($activeFilters)))
                            <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>Wyczyść
                            </a>
                        @endif
                    </div>

                    {{-- Okno wyboru tagów --}}
                    <div class="modal fade" id="tagPickerModal" tabindex="-1" aria-labelledby="tagPickerLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="tagPickerLabel">
                                        <i class="bi bi-tag me-2"></i>Wybierz tagi
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Zamknij"></button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted small mb-3">
                                        Kliknij tag, aby go zaznaczyć lub odznaczyć. Liczba przy tagu to liczba
                                        widocznych wpisów. Wyniki zawierają wszystkie zaznaczone tagi.
                                    </p>
                                    <div class="d-flex flex-wrap gap-2">
                                        @forelse ($tags as $tag)
                                            <input type="checkbox" class="btn-check" name="tags[]"
                                                id="filter-tag-{{ $tag->id }}" value="{{ $tag->slug }}"
                                                autocomplete="off"
                                                @checked(in_array($tag->slug, $activeFilters['tags'], true))>
                                            <label class="btn btn-outline-primary rounded-pill"
                                                for="filter-tag-{{ $tag->id }}">
                                                #{{ $tag->name }}
                                                <span class="badge bg-secondary">{{ $tag->posts_count }}</span>
                                            </label>
                                        @empty
                                            <p class="text-muted mb-0">Brak dostępnych tagów.</p>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary me-auto"
                                        id="clearTagsButton">Odznacz tagi</button>
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Zamknij</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel me-1"></i>Zastosuj filtry
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                @auth
                    <div class="border-top pt-3 mt-3">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label for="presetSelect" class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="bi bi-bookmark me-1"></i>Zapisane filtry
                                </label>
                                <select class="form-select" id="presetSelect">
                                    <option value="">Wybierz zestaw...</option>
                                    @foreach ($filterPresets as $preset)
                                        <option value="{{ route('posts.index', $preset->filters) }}">
                                            {{ $preset->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="presetName" class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="bi bi-bookmark-plus me-1"></i>Zapisz obecne filtry
                                </label>
                                <form method="POST" action="{{ route('filter-presets.store') }}"
                                    class="d-flex gap-2 align-items-start">
                                    @csrf
                                    @foreach ($activeFilters as $filterName => $filterValue)
                                        @if (is_array($filterValue))
                                            @foreach ($filterValue as $item)
                                                <input type="hidden" name="filters[{{ $filterName }}][]"
                                                    value="{{ $item }}">
                                            @endforeach
                                        @else
                                            <input type="hidden" name="filters[{{ $filterName }}]"
                                                value="{{ $filterValue }}">
                                        @endif
                                    @endforeach
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        id="presetName" name="name" maxlength="80" placeholder="Nazwa zestawu"
                                        value="{{ old('name') }}" required>
                                    <button type="submit" class="btn btn-outline-primary text-nowrap">
                                        <i class="bi bi-save me-1"></i>Zapisz
                                    </button>
                                </form>
                            </div>
                            <div class="col-md-3">
                                @if ($filterPresets->isNotEmpty())
                                    <div class="d-flex flex-wrap gap-1 justify-content-md-end">
                                        @foreach ($filterPresets as $preset)
                                            <form method="POST"
                                                action="{{ route('filter-presets.destroy', $preset) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    title="Usuń zestaw: {{ $preset->name }}"
                                                    onclick="return confirm('Czy na pewno usunąć ten zestaw filtrów?');">
                                                    <i class="bi bi-trash me-1"></i>{{ $preset->name }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if ($errors->any())
                            <div class="alert alert-danger py-2 small mt-2 mb-0">
                                <ul class="mb-0 ps-3">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                @endauth
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- AKTYWNE FILTRY --}}
        {{-- ============================================ --}}
        @if (!empty(array_filter($activeFilters ?? [])))
            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                <small class="text-muted">Aktywne filtry:</small>

                @if (!empty($activeFilters['search']))
                    <span class="badge bg-primary">Szukaj: "{{ $activeFilters['search'] }}"</span>
                @endif

                @if (!empty($activeFilters['category']))
                    <span class="badge bg-info text-dark">Kategoria: {{ $activeFilters['category'] }}</span>
                @endif

                @foreach ($activeFilters['tags'] as $tagSlug)
                    @php $activeTag = $tags->firstWhere('slug', $tagSlug); @endphp
                    @if ($activeTag)
                        <span class="badge bg-success">Tag: #{{ $activeTag->name }}</span>
                    @endif
                @endforeach

                @if (!empty($activeFilters['status']))
                    <span class="badge bg-warning text-dark">Status: {{ $activeFilters['status'] }}</span>
                @endif
            </div>
        @endif

        {{-- ============================================ --}}
        {{-- LICZNIK --}}
        {{-- ============================================ --}}
        <div class="mb-3 text-muted small">
            Znaleziono <strong>{{ $posts->total() }}</strong>
            {{ $posts->total() === 1 ? 'wpis' : ($posts->total() < 5 ? 'wpisy' : 'wpisów') }}
        </div>

        {{-- ============================================ --}}
        {{-- LISTA POSTÓW --}}
        {{-- ============================================ --}}
        @if ($posts->count() > 0)
            <div class="row g-4 justify-content-center">
                @foreach ($posts as $post)
                    @php $isAnonymous = ($post->source ?? 'post') === 'anonymous'; @endphp

                    <div class="col-12 col-sm-6 col-lg-4 d-flex">
                        <article class="card shadow-sm w-100 h-100 post-card">
                            {{-- Obrazek / placeholder --}}
                            @if (!empty($post->featured_image))
                                <img src="{{ Storage::url($post->featured_image) }}"
                                    class="card-img-top post-card-image" alt="{{ $post->title }}">
                            @else
                                <div class="post-card-image post-card-placeholder">
                                    <i class="bi bi-journal-text"></i>
                                </div>
                            @endif

                            {{-- Body --}}
                            <div class="card-body d-flex flex-column">
                                {{-- Badges --}}
                                <div class="mb-2 d-flex flex-wrap gap-1">
                                    @if ($isAnonymous)
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-incognito me-1"></i>Anonimowy
                                        </span>
                                    @endif

                                    @if ($post->moderation_status === 'pending')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock me-1"></i>Oczekuje
                                        </span>
                                    @elseif($post->moderation_status === 'rejected')
                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>Odrzucony
                                        </span>
                                    @elseif(!$isAnonymous && ($post->status ?? null) === 'draft')
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-pencil me-1"></i>Szkic
                                        </span>
                                    @endif

                                    <span class="badge bg-info text-dark">
                                        {{ $post->category }}
                                    </span>
                                </div>

                                {{-- Tytuł (ujednolicony link z stretched-link) --}}
                                <h5 class="card-title mb-2">
                                    <a href="{{ $isAnonymous ? route('anonymous.show', $post) : route('posts.show', $post) }}"
                                        class="text-decoration-none text-dark stretched-link-title">
                                        {{ $post->title }}
                                    </a>
                                </h5>

                                {{-- Excerpt --}}
                                <p class="card-text text-muted small flex-grow-1 mb-3">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($post->content), 140) }}
                                </p>

                                {{-- Tagi (tylko dla zwykłych postów + z-index!) --}}
                                @if (!$isAnonymous && method_exists($post, 'tags') && $post->relationLoaded('tags') && $post->tags->count() > 0)
                                    <div class="mb-3 d-flex flex-wrap gap-1 position-relative z-2">
                                        @foreach ($post->tags->take(3) as $tag)
                                            <a href="{{ route('posts.index', ['tags' => [$tag->slug]]) }}"
                                                class="badge bg-light text-dark text-decoration-none border">
                                                #{{ $tag->name }}
                                            </a>
                                        @endforeach
                                        @if ($post->tags->count() > 3)
                                            <span class="badge bg-light text-muted border">
                                                +{{ $post->tags->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                {{-- Vote buttons (tylko dla zwykłych postów) --}}
                                @if (!$isAnonymous)
                                    <div
                                        class="mt-2 d-flex justify-content-between align-items-center position-relative z-2">
                                        <x-vote-buttons type="post" :id="$post->id" :score="$post->vote_score ?? 0"
                                            :user-vote="$post->user_vote ?? null" size="sm" />
                                    </div>
                                @endif

                                {{-- Meta --}}
                                <div
                                    class="d-flex justify-content-between align-items-center small text-muted mt-auto pt-3 border-top">
                                    <div class="text-truncate me-2">
                                        @if ($isAnonymous)
                                            <i class="bi bi-incognito me-1"></i>
                                            <span class="fst-italic">Anonimowy</span>
                                        @else
                                            <i class="bi bi-person-circle me-1"></i>
                                            {{ $post->user?->name ?? 'Usunięty użytkownik' }}
                                        @endif
                                    </div>
                                    <div class="text-nowrap">
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $post->created_at->diffForHumans(null, true, true) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Footer (akcje autora — tylko dla zwykłych postów) --}}
                            @auth
                                @if (!$isAnonymous && auth()->id() === $post->user_id)
                                    <div class="card-footer bg-white border-top-0 d-flex gap-2 position-relative z-2">
                                        <a href="{{ route('posts.edit', $post) }}"
                                            class="btn btn-sm btn-outline-primary flex-grow-1">
                                            <i class="bi bi-pencil me-1"></i>Edytuj
                                        </a>
                                        <form method="POST" action="{{ route('posts.destroy', $post) }}"
                                            class="flex-grow-1"
                                            onsubmit="return confirm('Czy na pewno chcesz usunąć ten wpis?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                <i class="bi bi-trash me-1"></i>Usuń
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endauth
                        </article>
                    </div>
                @endforeach
            </div>

            {{-- Paginacja --}}
            <div class="d-flex justify-content-center mt-5">
                {{ $posts->links() }}
            </div>
        @else
            {{-- Empty state --}}
            <div class="text-center py-5">
                <i class="bi bi-journal-x display-1 text-muted"></i>
                <h3 class="mt-3">
                    @if (!empty(array_filter($activeFilters ?? [])))
                        Brak wyników
                    @else
                        Brak wpisów
                    @endif
                </h3>
                <p class="text-muted">
                    @if (!empty(array_filter($activeFilters ?? [])))
                        Nie znaleziono wpisów pasujących do filtrów.
                    @else
                        Bądź pierwszym, który podzieli się doświadczeniem!
                    @endif
                </p>

                @if (!empty(array_filter($activeFilters ?? [])))
                    <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary me-2">
                        <i class="bi bi-x-circle me-2"></i>Wyczyść filtry
                    </a>
                @endif

                @auth
                    @can('create', App\Models\Post::class)
                        <a href="{{ route('posts.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Napisz pierwszy wpis
                        </a>
                    @endcan
                @endauth
            </div>
        @endif

    </div>

    {{-- ============================================ --}}
    {{-- STYLES (dla równych kart) --}}
    {{-- ============================================ --}}
    @push('styles')
        <style>
            /* Karta – równa wysokość, hover */
            .post-card {
                transition: transform 0.15s ease, box-shadow 0.15s ease;
                overflow: hidden;
            }

            .post-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
            }

            /* Stała wysokość obrazka – niezależnie od proporcji */
            .post-card-image {
                width: 100%;
                height: 180px;
                object-fit: cover;
                display: block;
            }

            /* Placeholder gdy brak obrazka */
            .post-card-placeholder {
                display: flex;
                align-items: center;
                justify-content: center;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: #fff;
                font-size: 3.5rem;
            }

            /* Tytuł – maksymalnie 2 linie */
            .post-card .card-title a {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
                line-height: 1.3;
                min-height: 2.6em;
            }

            /* Excerpt – maksymalnie 3 linie */
            .post-card .card-text {
                display: -webkit-box;
                -webkit-line-clamp: 3;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Tagi – spójny rozmiar */
            .post-card .badge {
                font-size: 0.7rem;
                font-weight: 500;
            }

            .post-card {
                position: relative;
            }

            .post-card .stretched-link-title::after {
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                left: 0;
                z-index: 1;
                content: "";
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.getElementById('presetSelect')?.addEventListener('change', function () {
                if (this.value) {
                    window.location.href = this.value;
                }
            });

            document.getElementById('clearTagsButton')?.addEventListener('click', function () {
                document.querySelectorAll('#filterForm input[name="tags[]"]').forEach(function (checkbox) {
                    checkbox.checked = false;
                });
            });
        </script>
    @endpush
</x-layout.app>
