@extends('layouts.app')

@section('content')
    <h1>Login</h1>
    <form method="POST" action="/login">
        @csrf
        <div class="mb-3">
            <label class="form-label">E-Mail</label>
            <input class="form-control" type="email" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Passwort</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <button class="btn btn-primary" type="submit">Einloggen</button>
        <a class="btn btn-link" href="/forgot-password">Passwort vergessen?</a>
    </form>
@endsection
