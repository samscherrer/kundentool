@extends('layouts.app')

@section('content')
    <h1>Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</h1>

    <div class="mb-4">
        <h2 class="h5">Schätzung</h2>
        @if ($estimate && $estimate->status === 'sent')
            <div class="card mb-3">
                <div class="card-body">
                    <p><strong>Stunden:</strong> {{ $estimate->hours_estimate }}</p>
                    @if ($estimate->amount_estimate_cents)
                        <p><strong>Betrag:</strong> {{ $estimate->amount_estimate_cents }} {{ $estimate->currency }}</p>
                    @endif
                    <p>{{ $estimate->scope_note }}</p>
                    <form method="POST" action="/portal/estimates/{{ $estimate->id }}/decide">
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
                        <button class="btn btn-primary" type="submit">Senden</button>
                    </form>
                </div>
            </div>
        @else
            <p>Keine Schätzung verfügbar.</p>
        @endif
    </div>

    <div class="mb-3">
        <form method="POST" action="/portal/tickets/{{ $ticket->id }}/reply">
            @csrf
            <div class="mb-3">
                <label class="form-label">Antwort</label>
                <textarea class="form-control" name="body" rows="3" required></textarea>
            </div>
            <button class="btn btn-primary" type="submit">Antwort senden</button>
        </form>
    </div>

    <h2 class="h5">Verlauf</h2>
    @foreach ($publicStream as $event)
        <div class="mb-2">
            <strong>{{ $event->event_type }}</strong>
            <div class="text-muted small">{{ $event->created_at }}</div>
            <div>{{ $event->payload_json['body'] ?? json_encode($event->payload_json) }}</div>
        </div>
    @endforeach

    <div class="mt-4">
        <h2 class="h5">Deliverables</h2>
        <ul class="list-group">
            @foreach ($documents as $document)
                @if ($document->currentVersion && $document->currentVersion->customer_visible)
                    <li class="list-group-item">
                        {{ $document->title }}
                        <a class="btn btn-sm btn-outline-primary" href="/files/document-versions/{{ $document->currentVersion->id }}">Öffnen</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
@endsection
