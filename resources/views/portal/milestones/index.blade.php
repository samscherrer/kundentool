@extends('layouts.app')

@section('content')
    <h1>Meilensteine</h1>
    <ul class="list-group">
        @foreach ($milestones as $milestone)
            <li class="list-group-item">
                <a href="/portal/milestones/{{ $milestone->id }}">{{ $milestone->title }}</a>
            </li>
        @endforeach
    </ul>
@endsection
