@extends('layouts.app')

@section('content')
    <h1>Ticket erstellen</h1>
    <form method="POST" action="/portal/tickets">
        @csrf
        <div class="mb-3">
            <label class="form-label">Betreff</label>
            <input class="form-control" type="text" name="subject" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Priorität</label>
            <select class="form-select" name="priority">
                <option value="low">Niedrig</option>
                <option value="normal" selected>Normal</option>
                <option value="high">Hoch</option>
                <option value="urgent">Dringend</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Nachricht</label>
            <textarea class="form-control" name="message" required></textarea>
        </div>
        <button class="btn btn-primary" type="submit">Senden</button>
    </form>
@endsection
