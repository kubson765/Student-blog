@props(['type', 'id'])

<button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
    data-bs-target="#reportModal-{{ $type }}-{{ $id }}" title="Zgłoś tę treść">
    <i class="bi bi-flag me-1"></i>Zgłoś
</button>

<div class="modal fade" id="reportModal-{{ $type }}-{{ $id }}" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('report.store', ['type' => $type, 'id' => $id]) }}">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-flag me-2"></i>Zgłoś treść
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="report-reason-{{ $type }}-{{ $id }}"
                            class="form-label small fw-bold">
                            Powód <span class="text-danger">*</span>
                        </label>
                        <select name="reason" id="report-reason-{{ $type }}-{{ $id }}"
                            class="form-select" required>
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
                        <label for="report-description-{{ $type }}-{{ $id }}"
                            class="form-label small fw-bold">
                            Opis <span class="text-muted">(opcjonalnie)</span>
                        </label>
                        <textarea name="description" id="report-description-{{ $type }}-{{ $id }}" class="form-control"
                            rows="3" maxlength="1000" placeholder="Opisz szczegóły zgłoszenia..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Anuluj
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-flag me-1"></i>Zgłoś
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
