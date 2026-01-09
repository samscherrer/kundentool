@extends('layouts.app')

@section('content')
    <h1>Einladung akzeptieren</h1>
    <form method="POST" action="/invite/{{ $token }}/accept">
        @csrf
        <div class="mb-3">
            <label class="form-label">Name</label>
            <input class="form-control" type="text" name="name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Passwort</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <button class="btn btn-primary" type="submit">Registrieren</button>
    </form>
@endsection
