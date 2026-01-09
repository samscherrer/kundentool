@extends('layouts.app')

@section('content')
    <h1>Offerten</h1>
    <ul class="list-group">
        @foreach ($offers as $offer)
            <li class="list-group-item">
                <a href="/portal/offers/{{ $offer->id }}">#{{ $offer->id }} {{ $offer->title }}</a>
            </li>
        @endforeach
    </ul>
@endsection
