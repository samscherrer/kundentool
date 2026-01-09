@extends('layouts.app')

@section('content')
    <h1>{{ $milestone->title }}</h1>
    <p>Geplant: {{ $milestone->planned_at }}</p>
    <ul class="list-group">
        @foreach ($visibleItems as $item)
            <li class="list-group-item">
                {{ ucfirst($item['type']) }}: {{ $item['item']->title ?? $item['item']->subject ?? 'Item' }}
            </li>
        @endforeach
    </ul>
@endsection
