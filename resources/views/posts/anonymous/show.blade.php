<x-layout.app :title="$post->title">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">

                {{-- Breadcrumbs --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('home') }}">
                                <i class="bi bi-house-door me-1"></i>Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('posts.index') }}">Blog</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ Str::limit($post->title, 50) }}
                        </li>
                    </ol>
                </nav>

                {{-- Post Card --}}
                <article class="card shadow-sm">
                    <div class="card-body p-4 p-md-5">

                        {{-- Badges --}}
                        <div class="mb-3">
                            <span class="badge bg-secondary">
                                <i class="bi bi-incognito me-1"></i>Anonimowy
                            </span>
                            <span class="badge bg-info text-dark">
                                <i class="bi bi-folder me-1"></i>{{ $post->category }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <h1 class="display-5 fw-bold mb-3">{{ $post->title }}</h1>

                        {{-- Meta --}}
                        <div class="d-flex flex-wrap gap-3 text-muted mb-4 pb-3 border-bottom">
                            <div>
                                <i class="bi bi-incognito me-1"></i>
                                <em>Autor anonimowy</em>
                            </div>
                            <div>
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ $post->published_at?->format('d.m.Y') ?? $post->created_at->format('d.m.Y') }}
                            </div>
                            <div>
                                <i class="bi bi-clock me-1"></i>
                                {{ max(1, (int) ceil(str_word_count(strip_tags($post->content)) / 200)) }} min czytania
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="post-content" style="font-size: 1.1rem; line-height: 1.8;">
                            {!! nl2br(e($post->content)) !!}
                        </div>

                    </div>
                </article>

                {{-- Info o anonimowości --}}
                <div class="alert alert-light border mt-4">
                    <i class="bi bi-info-circle me-2"></i>
                    <small class="text-muted">
                        Ten wpis został opublikowany anonimowo. Nie zbieramy danych osobowych autora.
                    </small>
                </div>

                {{-- Back --}}
                <div class="mt-4 text-center">
                    <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Powrót do listy
                    </a>
                </div>

            </div>
        </div>
    </div>
</x-layout.app>
