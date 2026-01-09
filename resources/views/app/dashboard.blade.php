@extends('layouts.app')

@section('content')
    <h1>Internes Dashboard</h1>
    <div class="list-group">
        <a class="list-group-item" href="/app/tickets">Tickets</a>
        <a class="list-group-item" href="/app/offers">Offerten</a>
        <a class="list-group-item" href="/app/orders">Aufträge</a>
        <a class="list-group-item" href="/app/milestones">Meilensteine</a>
    </div>
@endsection
