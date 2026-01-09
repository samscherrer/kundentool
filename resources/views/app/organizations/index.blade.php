@extends('layouts.app')

@section('content')
    <h1>Organisationen</h1>
    <ul class="list-group">
        @foreach ($organizations as $organization)
            <li class="list-group-item">
                <a href="/app/organizations/{{ $organization->id }}">{{ $organization->name }}</a>
            </li>
        @endforeach
    </ul>
@endsection
