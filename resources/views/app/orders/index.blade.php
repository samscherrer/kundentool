@extends('layouts.app')

@section('content')
    <h1>Aufträge</h1>
    <ul class="list-group">
        @foreach ($orders as $order)
            <li class="list-group-item">
                <a href="/app/orders/{{ $order->id }}">#{{ $order->id }} {{ $order->title }}</a>
            </li>
        @endforeach
    </ul>
@endsection
