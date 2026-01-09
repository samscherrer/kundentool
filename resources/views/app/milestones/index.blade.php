@extends('layouts.app')

@section('content')
    <h1>Meilensteine</h1>
    <a class="btn btn-primary mb-3" href="/app/milestones/create">Neuer Meilenstein</a>
    <ul class="list-group">
        @foreach ($milestones as $milestone)
            <li class="list-group-item">
                <a href="/app/milestones/{{ $milestone->id }}">{{ $milestone->title }}</a>
            </li>
        @endforeach
    </ul>
@endsection
