@extends('layouts.app')

@section('content')
    <h1>Schätzung für Ticket #{{ $ticket->id }}</h1>
    <form method="POST" action="/app/tickets/{{ $ticket->id }}/estimate/send">
        @csrf
        <div class="mb-3">
            <label class="form-label">Stunden</label>
            <input class="form-control" type="number" step="0.25" name="hours_estimate" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Betrag (Rappen)</label>
            <input class="form-control" type="number" name="amount_estimate_cents">
        </div>
        <div class="mb-3">
            <label class="form-label">Währung</label>
            <input class="form-control" type="text" name="currency" value="CHF">
        </div>
        <div class="mb-3">
            <label class="form-label">Hinweis</label>
            <textarea class="form-control" name="scope_note"></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Schätzung senden</button>
    </form>
@endsection
