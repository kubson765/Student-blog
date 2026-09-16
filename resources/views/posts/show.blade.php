<x-layout.app :title="$post->title">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <!-- Breadcrumbs -->
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
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ Str::limit($post->title, 50) }}
                        </li>
                    </ol>
                </nav>

                <!-- Success message -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Post Card -->
                <article class="card shadow-sm">
                    @if ($post->featured_image)
                        <img src="{{ Storage::url($post->featured_image) }}" class="card-img-top"
                            alt="{{ $post->title }}" style="max-height: 400px; object-fit: cover;">
                    @endif

                    <div class="card-body p-4 p-md-5">
                        <!-- Status badges -->
                        <div class="mb-3">
                            @if ($post->isDraft())
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-pencil me-1"></i>Draft
                                </span>
                            @elseif($post->isPublished())
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Published
                                </span>
                            @else
                                <span class="badge bg-secondary">
                                    <i class="bi bi-archive me-1"></i>Archived
                                </span>
                            @endif

                            <span class="badge bg-info text-dark">
                                <i class="bi bi-folder me-1"></i>{{ $post->category }}
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 class="display-5 fw-bold mb-3">{{ $post->title }}</h1>

                        <!-- Meta info -->
                        <div class="d-flex flex-wrap gap-3 text-muted mb-4 pb-3 border-bottom">
                            <div>
                                <i class="bi bi-person-circle me-1"></i>
                                <strong>{{ $post->author_name }}</strong>
                            </div>
                            <div>
                                <i class="bi bi-calendar3 me-1"></i>
                                @if ($post->published_at)
                                    {{ $post->published_at->format('F d, Y') }}
                                @else
                                    {{ $post->created_at->format('F d, Y') }}
                                @endif
                            </div>
                            <div>
                                <i class="bi bi-clock me-1"></i>
                                {{ $post->reading_time }} min read
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="post-content" style="font-size: 1.1rem; line-height: 1.8;">
                            {!! nl2br(e($post->content)) !!}
                        </div>

                        <!-- Tags -->
                        @if ($post->tags->count() > 0)
                            <div class="mt-5 pt-4 border-top">
                                <h6 class="text-muted mb-3">
                                    <i class="bi bi-tags me-1"></i>Tags
                                </h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($post->tags as $tag)
                                        <a href="{{ route('posts.index', ['tag' => $tag->slug]) }}"
                                            class="badge bg-secondary text-decoration-none">
                                            #{{ $tag->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Author actions -->
                    @auth
                        @if (auth()->id() === $post->user_id)
                            <div class="card-footer bg-light d-flex justify-content-end gap-2 p-3">
                                <a href="{{ route('posts.edit', $post) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-pencil me-2"></i>Edit Post
                                </a>
                                <form method="POST" action="{{ route('posts.destroy', $post) }}"
                                    onsubmit="return confirm('Are you sure you want to delete this post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </div>
                        @endif
                    @endauth
                </article>

                <!-- Comments section (placeholder na przyszłość) -->
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="bi bi-chat-dots me-2"></i>
                            Comments ({{ $post->comments->count() ?? 0 }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if (isset($post->comments) && $post->comments->count() > 0)
                            @foreach ($post->comments as $comment)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center mb-2">
                                        <strong>{{ $comment->author_name }}</strong>
                                        <small class="text-muted ms-2">
                                            {{ $comment->created_at->diffForHumans() }}
                                        </small>
                                    </div>
                                    <p class="mb-0">{{ $comment->content }}</p>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center py-3">
                                <i class="bi bi-chat-square-text display-6 d-block mb-2"></i>
                                No comments yet. Be the first to share your thoughts!
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Back to list -->
                <div class="mt-4 text-center">
                    <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to All Posts
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
