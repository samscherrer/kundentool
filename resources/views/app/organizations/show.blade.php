@extends('layouts.app')

@section('content')
    <h1>{{ $organization->name }}</h1>
    <p>Organisation-ID: {{ $organization->id }}</p>
@endsection
