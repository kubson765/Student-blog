<x-layout.app title="Create New Post">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-plus-circle me-2"></i>Create New Post
                        </h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('posts.store') }}">
                            @csrf

                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">
                                    Title <span class="text-danger">*</span>
                                </label>
                                <input type="text"
                                       class="form-control form-control-lg @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title') }}"
                                       placeholder="Enter a descriptive title..."
                                       required
                                       autofocus>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category & Status -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="category" class="form-label">
                                        Category <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('category') is-invalid @enderror"
                                            id="category"
                                            name="category"
                                            required>
                                        <option value="">Choose category...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}"
                                                    {{ old('category') === $category ? 'selected' : '' }}>
                                                {{ $category }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror"
                                            id="status"
                                            name="status"
                                            required>
                                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>
                                            📝 Draft (Save for later)
                                        </option>
                                        <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>
                                            🚀 Published (Publish now)
                                        </option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tags -->
                            <div class="mb-3">
                                <label for="tags" class="form-label">
                                    Tagi <span class="text-muted">(opcjonalne)</span>
                                </label>
                                <select class="form-select @error('tags') is-invalid @enderror"
                                        id="tags"
                                        name="tags[]"
                                        multiple
                                        size="8">
                                    @foreach($tags ?? [] as $tag)
                                        <option value="{{ $tag->id }}"
                                                {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                            #{{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Przytrzymaj Ctrl (Cmd na Mac) aby wybrać wiele tagów.
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    Content <span class="text-danger">*</span>
                                </label>
                                <textarea class="form-control @error('content') is-invalid @enderror"
                                          id="content"
                                          name="content"
                                          rows="15"
                                          placeholder="Write your post content here..."
                                          required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Markdown supported. Minimum 50 characters.
                                </div>
                            </div>

                            <!-- Published at (opcjonalne) -->
                            <div class="mb-3">
                                <label for="published_at" class="form-label">
                                    Schedule Publication <span class="text-muted">(optional)</span>
                                </label>
                                <input type="datetime-local"
                                       class="form-control @error('published_at') is-invalid @enderror"
                                       id="published_at"
                                       name="published_at"
                                       value="{{ old('published_at') }}">
                                @error('published_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Leave empty to publish immediately when status is "Published".
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-flex gap-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Save Post
                                </button>
                                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary btn-lg">
                                    <i class="bi bi-arrow-left me-2"></i>Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
