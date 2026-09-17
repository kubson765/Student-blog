<x-layout.app title="Anonimowy wpis">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0">
                            <i class="bi bi-incognito me-2"></i>Anonimowy wpis
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Anonimowy wpis</strong> – nie musisz się logować.
                            Twój wpis trafi do moderacji przed publikacją.
                            Nie zbieramy Twoich danych osobowych.
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('posts.anonymous.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="title" class="form-label">Tytuł</label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror"
                                    id="title" name="title" value="{{ old('title') }}" minlength="5"
                                    maxlength="255" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="category" class="form-label">Kategoria</label>
                                <select name="category" id="category" class="form-select">
                                    <option value="Ogólne">Ogólne</option>
                                    <option value="Życie studenckie">Życie studenckie</option>
                                    <option value="Egzaminy i sesja">Egzaminy i sesja</option>
                                    <option value="Wykłady i ćwiczenia">Wykłady i ćwiczenia</option>
                                    <option value="Kampus i wydarzenia">Kampus i wydarzenia</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">Treść</label>
                                <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="10"
                                    minlength="50" maxlength="10000" required>{{ old('content') }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    Minimum 50 znaków. Wpisy są moderowane przed publikacją.
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-send me-2"></i>Wyślij do moderacji
                                </button>
                                <a href="{{ route('posts.index') }}" class="btn btn-outline-secondary">
                                    Anuluj
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.app>
