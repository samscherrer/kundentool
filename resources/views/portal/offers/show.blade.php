@extends('layouts.app')

@section('content')
    <h1>Offerte #{{ $offer->id }} - {{ $offer->title }}</h1>
    <ul class="list-group">
        @foreach ($positions as $position)
            <li class="list-group-item">
                <strong>{{ $position->title }}</strong>
                <div>{{ $position->description }}</div>
                <div>Status: {{ $position->status }}</div>
                <form method="POST" action="/portal/offer-positions/{{ $position->id }}/decide" class="mt-2">
                    @csrf
                    <select class="form-select" name="decision">
                        <option value="approved">Freigeben</option>
                        <option value="rejected">Ablehnen</option>
                    </select>
                    <button class="btn btn-primary mt-2" type="submit">Entscheiden</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection
