@extends('layouts.app')

@section('content')
    <h1>Auftrag #{{ $order->id }}</h1>
    <p>Status: {{ $order->status }}</p>
@endsection
