@extends('layouts.app')

@section('content')
    <h1>Review: {{ $document->title }}</h1>
    <div class="mb-3">
        <a class="btn btn-outline-primary" href="/files/document-versions/{{ $review->document_version_id }}">Datei öffnen</a>
    </div>

    <div id="preview" class="mb-3">
        @if (str_starts_with($review->version->mime_type, 'application/pdf'))
            <div id="pdf-viewer" class="border p-2 bg-white"></div>
        @elseif (str_starts_with($review->version->mime_type, 'image/'))
            <img class="img-fluid border" src="/files/document-versions/{{ $review->document_version_id }}" alt="Preview">
        @elseif (str_starts_with($review->version->mime_type, 'video/'))
            <video class="w-100" controls>
                <source src="/files/document-versions/{{ $review->document_version_id }}" type="{{ $review->version->mime_type }}">
            </video>
        @else
            <p>Keine Vorschau verfügbar. Bitte Datei herunterladen.</p>
        @endif
    </div>

    <form method="POST" action="/portal/reviews/{{ $review->id }}/comments" class="mb-4">
        @csrf
        <input type="hidden" name="context_type" id="context_type" value="none">
        <input type="hidden" name="context_json[page]" id="context_page">
        <div class="mb-3">
            <label class="form-label">Kommentar</label>
            <textarea class="form-control" name="body" required></textarea>
        </div>
        <button class="btn btn-secondary" type="submit">Kommentar hinzufügen</button>
    </form>

    <form method="POST" action="/portal/reviews/{{ $review->id }}/decide" class="mb-4">
        @csrf
        <div class="mb-3">
            <label class="form-label">Entscheidung</label>
            <select class="form-select" name="decision">
                <option value="approved">Freigeben</option>
                <option value="approved_with_changes">Freigeben mit Änderungen</option>
                <option value="changes_requested">Änderungen verlangen</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Kommentar</label>
            <textarea class="form-control" name="decision_note"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Entscheidung senden</button>
    </form>

    <h2 class="h5">Kommentare</h2>
    <ul class="list-group">
        @foreach ($comments as $comment)
            <li class="list-group-item">
                {{ $comment->body }}
            </li>
        @endforeach
    </ul>

    @if (str_starts_with($review->version->mime_type, 'application/pdf'))
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.0.379/pdf.min.js"></script>
        <script src="/js/review.js"></script>
        <script>
            window.reviewFileUrl = '/files/document-versions/{{ $review->document_version_id }}';
        </script>
    @endif
@endsection
