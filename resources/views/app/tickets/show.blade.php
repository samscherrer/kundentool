@extends('layouts.app')

@section('content')
    <h1>Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</h1>
    <p>Status: {{ $ticket->status }} | Priorität: {{ $ticket->priority }}</p>

    <div class="mb-3">
        <a class="btn btn-outline-primary" href="/app/tickets/{{ $ticket->id }}/estimate/create">Schätzung erstellen</a>
        <form class="d-inline" method="POST" action="/app/tickets/{{ $ticket->id }}/task-from-ticket">
            @csrf
            <button class="btn btn-outline-secondary" type="submit">Task aus Ticket erstellen</button>
        </form>
    </div>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#public">Kunde</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#internal">Intern</a></li>
    </ul>
    <div class="tab-content border border-top-0 p-3 bg-white">
        <div class="tab-pane fade show active" id="public">
            <form method="POST" action="/app/tickets/{{ $ticket->id }}/public-message">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nachricht an Kunde</label>
                    <textarea class="form-control" name="body" rows="3" required></textarea>
                </div>
                <button class="btn btn-primary" type="submit">Senden</button>
            </form>
            <hr>
            @foreach ($publicStream as $event)
                <div class="mb-2">
                    <strong>{{ $event->event_type }}</strong>
                    <div class="text-muted small">{{ $event->created_at }}</div>
                    <div>{{ $event->payload_json['body'] ?? json_encode($event->payload_json) }}</div>
                </div>
            @endforeach
        </div>
        <div class="tab-pane fade" id="internal">
            <form method="POST" action="/app/tickets/{{ $ticket->id }}/internal-note">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Interne Notiz</label>
                    <textarea class="form-control" name="body" rows="3" required></textarea>
                </div>
                <button class="btn btn-secondary" type="submit">Speichern</button>
            </form>
            <hr>
            @foreach ($internalStream as $event)
                <div class="mb-2">
                    <strong>{{ $event->event_type }}</strong>
                    <div class="text-muted small">{{ $event->created_at }}</div>
                    <div>{{ $event->payload_json['body'] ?? json_encode($event->payload_json) }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
