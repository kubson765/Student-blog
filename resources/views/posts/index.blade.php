<x-layout.app title="Blog Studencki">
    <div class="container py-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="display-5 fw-bold">
                    <i class="bi bi-journal-text text-primary me-2"></i>Blog Studencki
                </h1>
                <p class="text-muted mb-0">Odkryj doświadczenia innych studentów</p>
            </div>
            @auth
                @can('create', App\Models\Post::class)
                    <a href="{{ route('posts.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle me-2"></i>Nowy wpis
                    </a>
                @endcan
            @endauth
        </div>

        <!-- Success message -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- ============================================ -->
        <!-- FILTRY                                        -->
        <!-- ============================================ -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('posts.index') }}" id="filterForm">
                    <div class="row g-3">
                        <!-- Wyszukiwanie -->
                        <div class="col-md-4">
                            <label for="search" class="form-label small fw-bold">
                                <i class="bi bi-search me-1"></i>Szukaj
                            </label>
                            <input type="text" class="form-control" id="search" name="search"
                                value="{{ $activeFilters['search'] ?? '' }}" placeholder="Tytuł lub treść...">
                        </div>

                        <!-- Kategoria -->
                        <div class="col-md-3">
                            <label for="category" class="form-label small fw-bold">
                                <i class="bi bi-folder me-1"></i>Kategoria
                            </label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Wszystkie</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}"
                                        {{ ($activeFilters['category'] ?? '') === $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tag -->
                        <div class="col-md-3">
                            <label for="tag" class="form-label small fw-bold">
                                <i class="bi bi-tag me-1"></i>Tag
                            </label>
                            <select class="form-select" id="tag" name="tag">
                                <option value="">Wszystkie</option>
                                @foreach ($tags as $tag)
                                    <option value="{{ $tag->slug }}"
                                        {{ ($activeFilters['tag'] ?? '') === $tag->slug ? 'selected' : '' }}>
                                        #{{ $tag->name }} ({{ $tag->posts_count }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Sortowanie -->
                        <div class="col-md-2">
                            <label for="sort" class="form-label small fw-bold">
                                <i class="bi bi-sort-down me-1"></i>Sortuj
                            </label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="latest"
                                    {{ ($activeFilters['sort'] ?? 'latest') === 'latest' ? 'selected' : '' }}>
                                    Najnowsze
                                </option>
                                <option value="oldest"
                                    {{ ($activeFilters['sort'] ?? '') === 'oldest' ? 'selected' : '' }}>
                                    Najstarsze
                                </option>
                                <option value="title"
                                    {{ ($activeFilters['sort'] ?? '') === 'title' ? 'selected' : '' }}>
                                    Tytuł (A-Z)
                                </option>
                                <option value="popular"
                                    {{ ($activeFilters['sort'] ?? '') === 'popular' ? 'selected' : '' }}>
                                    Popularne
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Status (tylko dla zalogowanych) -->
                    @auth
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label for="status" class="form-label small fw-bold">
                                    <i class="bi bi-funnel me-1"></i>Status
                                </label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Wszystkie widoczne</option>
                                    <option value="published"
                                        {{ ($activeFilters['status'] ?? '') === 'published' ? 'selected' : '' }}>
                                        Opublikowane
                                    </option>
                                    <option value="draft"
                                        {{ ($activeFilters['status'] ?? '') === 'draft' ? 'selected' : '' }}>
                                        Moje szkice
                                    </option>
                                    <option value="archived"
                                        {{ ($activeFilters['status'] ?? '') === 'archived' ? 'selected' : '' }}>
                                        Zarchiwizowane
                                    </option>
                                </select>
                            </div>
                        </div>
                    @endauth

                    <!-- Przyciski -->
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
                </form>
            </div>
        </div>

        <!-- Aktywne filtry (chips) -->
        @if (!empty(array_filter($activeFilters)))
            <div class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                <small class="text-muted">Aktywne filtry:</small>

                @if (!empty($activeFilters['search']))
                    <span class="badge bg-primary">
                        Szukaj: "{{ $activeFilters['search'] }}"
                    </span>
                @endif

                @if (!empty($activeFilters['category']))
                    <span class="badge bg-info text-dark">
                        Kategoria: {{ $activeFilters['category'] }}
                    </span>
                @endif

                @if (!empty($activeFilters['tag']))
                    @php
                        $activeTag = $tags->firstWhere('slug', $activeFilters['tag']);
                    @endphp
                    @if ($activeTag)
                        <span class="badge bg-success">
                            Tag: #{{ $activeTag->name }}
                        </span>
                    @endif
                @endif

                @if (!empty($activeFilters['status']))
                    <span class="badge bg-warning text-dark">
                        Status: {{ $activeFilters['status'] }}
                    </span>
                @endif
            </div>
        @endif

        <!-- Wyniki wyszukiwania -->
        <div class="mb-3 text-muted small">
            Znaleziono <strong>{{ $posts->total() }}</strong>
            {{ $posts->total() === 1 ? 'wpis' : ($posts->total() < 5 ? 'wpisy' : 'wpisów') }}
        </div>

        <!-- Posts grid -->
        @if ($posts->count() > 0)
            <div class="row">
                @foreach ($posts as $post)
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100 shadow-sm">
                            @if ($post->featured_image)
                                <img src="{{ Storage::url($post->featured_image) }}" class="card-img-top"
                                    alt="{{ $post->title }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="text-white d-flex align-items-center justify-content-center"
                                    style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <i class="bi bi-journal-text" style="font-size: 4rem;"></i>
                                </div>
                            @endif

                            <div class="card-body d-flex flex-column">
                                <!-- Status badge -->
                                <div class="mb-2">
                                    @if ($post->isDraft())
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-pencil me-1"></i>Szkic
                                        </span>
                                    @elseif($post->isPublished())
                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>Opublikowany
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="bi bi-archive me-1"></i>Zarchiwizowany
                                        </span>
                                    @endif

                                    <span class="badge bg-info text-dark">
                                        {{ $post->category }}
                                    </span>
                                </div>

                                <!-- Title -->
                                <h5 class="card-title">
                                    <a href="{{ route('posts.show', $post) }}"
                                        class="text-decoration-none text-dark">
                                        {{ $post->title }}
                                    </a>
                                </h5>

                                <!-- Excerpt -->
                                <p class="card-text text-muted small flex-grow-1">
                                    {{ $post->excerpt }}
                                </p>

                                <!-- Tags -->
                                @if ($post->tags->count() > 0)
                                    <div class="mb-2 d-flex flex-wrap gap-1">
                                        @foreach ($post->tags->take(3) as $tag)
                                            <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                                                class="badge bg-secondary text-decoration-none"
                                                style="font-size: 0.7rem;">
                                                #{{ $tag->name }}
                                            </a>
                                        @endforeach
                                        @if ($post->tags->count() > 3)
                                            <span class="badge bg-light text-muted" style="font-size: 0.7rem;">
                                                +{{ $post->tags->count() - 3 }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Meta -->
                                <div class="d-flex justify-content-between align-items-center small text-muted mt-2">
                                    <div>
                                        <i class="bi bi-person-circle me-1"></i>
                                        {{ $post->user->name }}
                                    </div>
                                    <div>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $post->reading_time }} min
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            @auth
                                @if (auth()->id() === $post->user_id)
                                    <div class="card-footer bg-white border-top-0 d-flex gap-2">
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
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $posts->links() }}
            </div>
        @else
            <!-- Empty state -->
            <div class="text-center py-5">
                <i class="bi bi-journal-x display-1 text-muted"></i>
                <h3 class="mt-3">
                    @if (!empty(array_filter($activeFilters)))
                        Brak wyników
                    @else
                        Brak wpisów
                    @endif
                </h3>
                <p class="text-muted">
                    @if (!empty(array_filter($activeFilters)))
                        Nie znaleziono wpisów pasujących do filtrów.
                    @else
                        Bądź pierwszym, który podzieli się doświadczeniem!
                    @endif
                </p>

                @if (!empty(array_filter($activeFilters)))
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
</x-layout.app>
