@extends('layouts.app')

@section('content')
    <h1>Schätzungen</h1>
    <ul class="list-group">
        @foreach ($estimates as $estimate)
            <li class="list-group-item">
                Ticket #{{ $estimate->ticket_id }} - {{ $estimate->hours_estimate }} Stunden ({{ $estimate->status }})
            </li>
        @endforeach
    </ul>
@endsection
