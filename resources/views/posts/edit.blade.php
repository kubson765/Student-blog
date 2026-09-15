<x-layout.app :title="'Edit: ' . $post->title">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">
                            <i class="bi bi-pencil-square me-2"></i>Edit Post
                        </h4>
                        <span class="badge bg-dark">
                            ID: {{ $post->id }}
                        </span>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('posts.update', $post) }}">
                            @csrf
                            @method('PUT')

                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    Title <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title', $post->title) }}"
                                       required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category & Status -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">Category</label>
                                    <select class="form-select @error('category') is-invalid @enderror"
                                            id="category"
                                            name="category"
                                            required>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}"
                                                    {{ old('category', $post->category) === $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status"
                                            required>
                                        <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>
                                            📝 Draft
                                        </option>
                                        <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>
                                            🚀 Published
                                        </option>
                                        <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>
                                            📦 Archived
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="mb-3">
                                <label for="content" class="form-label">Content</label>
                                <textarea class="form-control @error('content') is-invalid @enderror"
                                          id="content"
                                          name="content"
                                          rows="15"
                                          required>{{ old('content', $post->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Published at -->
                            <div class="mb-3">
                                <label for="published_at" class="form-label">
                                    Published Date
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('published_at') is-invalid @enderror"
                                       id="published_at"
                                       name="published_at"
                                       value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}">
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Info -->
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>Created:</strong> {{ $post->created_at->format('M d, Y H:i') }}
                                @if($post->updated_at->ne($post->created_at))
                                    | <strong>Last updated:</strong> {{ $post->updated_at->diffForHumans() }}
                                @endif
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Update Post
                                </button>
                                <a href="{{ route('posts.show', $post) }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                        </form>

                        <!-- Delete section -->
                        @can('delete', $post)
                            <hr class="my-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-danger mb-1">
                                        <i class="bi bi-exclamation-triangle me-2"></i>Danger Zone
                                    </h6>
                                    <p class="text-muted small mb-0">
                                        Once deleted, this post cannot be recovered.
                                    </p>
                                </div>
                                <form method="POST"
                                      action="{{ route('posts.destroy', $post) }}"
                                      onsubmit="return confirm('Are you sure you want to permanently delete this post?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash me-2"></i>Delete Post
                                    </button>
                                </form>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
