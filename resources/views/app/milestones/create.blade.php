@extends('layouts.app')

@section('content')
    <h1>Meilenstein erstellen</h1>
    <form method="POST" action="/app/milestones">
        @csrf
        <div class="mb-3">
            <label class="form-label">Organization ID</label>
            <input class="form-control" type="number" name="organization_id" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Titel</label>
            <input class="form-control" type="text" name="title" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Typ</label>
            <select class="form-select" name="type">
                <option value="update">Update</option>
                <option value="release">Release</option>
                <option value="handover">Übergabe</option>
                <option value="go_live">Go Live</option>
                <option value="other">Andere</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Geplant am</label>
            <input class="form-control" type="datetime-local" name="planned_at" required>
        </div>
        <button class="btn btn-primary" type="submit">Speichern</button>
    </form>
@endsection
