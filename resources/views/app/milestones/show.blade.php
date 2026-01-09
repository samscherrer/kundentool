@extends('layouts.app')

@section('content')
    <h1>{{ $milestone->title }}</h1>
    <p>Geplant: {{ $milestone->planned_at }} | Status: {{ $milestone->status }}</p>

    <form method="POST" action="/app/milestones/{{ $milestone->id }}/items" class="mb-4">
        @csrf
        <h2 class="h5">Item hinzufügen</h2>
        <div class="mb-3">
            <label class="form-label">Typ</label>
            <select class="form-select" name="item_type">
                <option value="ticket">Ticket</option>
                <option value="task">Task</option>
                <option value="order">Auftrag</option>
                <option value="document">Dokument</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Item ID</label>
            <input class="form-control" type="number" name="item_id" required>
        </div>
        <button class="btn btn-secondary" type="submit">Hinzufügen</button>
    </form>

    <h2 class="h5">Items</h2>
    <ul class="list-group mb-3">
        @foreach ($items as $item)
            <li class="list-group-item">{{ $item->item_type }} #{{ $item->item_id }}</li>
        @endforeach
    </ul>

    <form method="POST" action="/app/milestones/{{ $milestone->id }}/complete">
        @csrf
        <button class="btn btn-success" type="submit">Abschließen</button>
    </form>
@endsection
