@extends('layouts.app')

@section('content')
    <h1>Tickets</h1>
    <ul class="list-group">
        @foreach ($tickets as $ticket)
            <li class="list-group-item">
                <a href="/app/tickets/{{ $ticket->id }}">#{{ $ticket->id }} {{ $ticket->subject }}</a>
            </li>
        @endforeach
    </ul>
@endsection
