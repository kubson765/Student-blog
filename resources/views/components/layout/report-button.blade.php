@props(['type', 'id'])

@php
    $modalId = 'reportModal_' . $type . '_' . $id;
@endphp

<div>
    <!-- Przycisk wyzwalający modal Bootstraps -->
    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
        data-bs-target="#{{ $modalId }}">
        <i class="bi bi-flag me-1"></i>Zgłoś
    </button>

    <!-- Bootstrap Modal -->
    <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('report.store', ['type' => $type, 'id' => $id]) }}">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="{{ $modalId }}Label">Zgłoś treść</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="reason_{{ $modalId }}" class="form-label">Powód</label>
                            <select id="reason_{{ $modalId }}" name="reason" class="form-select" required>
                                <option value="">Wybierz powód...</option>
                                <option value="spam">Spam / reklama</option>
                                <option value="harassment">Nękanie</option>
                                <option value="hate_speech">Mowa nienawiści</option>
                                <option value="sexual_content">Treści seksualne</option>
                                <option value="violence">Przemoc</option>
                                <option value="misinformation">Dezinformacja</option>
                                <option value="copyright">Naruszenie praw autorskich</option>
                                <option value="other">Inne</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="description_{{ $modalId }}" class="form-label">Opis (opcjonalnie)</label>
                            <textarea id="description_{{ $modalId }}" name="description" class="form-control" rows="3" maxlength="1000"
                                placeholder="Opisz szczegóły..."></textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anuluj</button>
                        <button type="submit" class="btn btn-danger">Zgłoś</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
