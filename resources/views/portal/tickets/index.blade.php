@extends('layouts.app')

@section('content')
    <h1>Tickets</h1>
    <a class="btn btn-primary mb-3" href="/portal/tickets/create">Ticket erstellen</a>
    <ul class="list-group">
        @foreach ($tickets as $ticket)
            <li class="list-group-item">
                <a href="/portal/tickets/{{ $ticket->id }}">#{{ $ticket->id }} {{ $ticket->subject }}</a>
            </li>
        @endforeach
    </ul>
@endsection
