@extends('layouts.app')

@section('content')
    <h1>Offerte erstellen</h1>
    <form method="POST" action="/app/offers">
        @csrf
        <div class="mb-3">
            <label class="form-label">Organization ID</label>
            <input class="form-control" type="number" name="organization_id" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Titel</label>
            <input class="form-control" type="text" name="title" required>
        </div>
        <button class="btn btn-primary" type="submit">Anlegen</button>
    </form>
@endsection
