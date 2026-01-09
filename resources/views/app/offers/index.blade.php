@extends('layouts.app')

@section('content')
    <h1>Offerten</h1>
    <a class="btn btn-primary mb-3" href="/app/offers/create">Neue Offerte</a>
    <ul class="list-group">
        @foreach ($offers as $offer)
            <li class="list-group-item">
                <a href="/app/offers/{{ $offer->id }}/edit">#{{ $offer->id }} {{ $offer->title }}</a>
            </li>
        @endforeach
    </ul>
@endsection
