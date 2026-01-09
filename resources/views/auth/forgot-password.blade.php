@extends('layouts.app')

@section('content')
    <h1>Passwort vergessen</h1>
    <form method="POST" action="/forgot-password">
        @csrf
        <div class="mb-3">
            <label class="form-label">E-Mail</label>
            <input class="form-control" type="email" name="email" required>
        </div>
        <button class="btn btn-primary" type="submit">Link senden</button>
    </form>
@endsection
