@extends('layouts.app')

@section('content')
    <h1>Offerte #{{ $offer->id }}</h1>

    <form method="POST" action="/app/offers/{{ $offer->id }}/positions" class="mb-4">
        @csrf
        <h2 class="h5">Neue Position</h2>
        <div class="mb-3">
            <label class="form-label">Titel</label>
            <input class="form-control" type="text" name="title" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Beschreibung</label>
            <textarea class="form-control" name="description"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Preistyp</label>
            <select class="form-select" name="pricing_type">
                <option value="fixed">Fixpreis</option>
                <option value="time_and_material">Time &amp; Material</option>
                <option value="hours_package">Stundenpaket</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Budget Stunden</label>
            <input class="form-control" type="number" step="0.25" name="budget_hours">
        </div>
        <div class="mb-3">
            <label class="form-label">Budget Betrag (Rappen)</label>
            <input class="form-control" type="number" name="budget_amount_cents">
        </div>
        <button class="btn btn-secondary" type="submit">Position hinzufügen</button>
    </form>

    <h2 class="h5">Positionen</h2>
    <ul class="list-group mb-3">
        @foreach ($positions as $position)
            <li class="list-group-item">
                <strong>{{ $position->title }}</strong>
                <div class="text-muted">{{ $position->pricing_type }} | Status: {{ $position->status }}</div>
            </li>
        @endforeach
    </ul>

    <form method="POST" action="/app/offers/{{ $offer->id }}/send">
        @csrf
        <button class="btn btn-primary" type="submit">An Kunde senden</button>
    </form>
@endsection
