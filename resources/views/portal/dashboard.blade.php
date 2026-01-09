@extends('layouts.app')

@section('content')
    <h1>Kundenportal</h1>
    <div class="list-group">
        <a class="list-group-item" href="/portal/tickets">Tickets</a>
        <a class="list-group-item" href="/portal/offers">Offerten</a>
        <a class="list-group-item" href="/portal/estimates">Schätzungen</a>
        <a class="list-group-item" href="/portal/milestones">Meilensteine</a>
    </div>
@endsection
