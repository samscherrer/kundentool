@extends('layouts.app')

@section('content')
    <h1>Passwort zurücksetzen</h1>
    <form method="POST" action="/reset-password">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="mb-3">
            <label class="form-label">E-Mail</label>
            <input class="form-control" type="email" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Neues Passwort</label>
            <input class="form-control" type="password" name="password" required>
        </div>
        <button class="btn btn-primary" type="submit">Zurücksetzen</button>
    </form>
@endsection
