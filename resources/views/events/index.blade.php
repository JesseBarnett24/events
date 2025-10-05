@extends('layouts.master')

@section('Title')
  Item list
@endsection

@section('content')
<h1>Event List</h1>
<ul>
    @foreach ($events as $event)
        <li>
            <a href="{{ url('event/' . $event->id) }}">{{ $event->name }}</a>
        </li>
    @endforeach
</ul>

<!-- Create new event button -->
<p>
  <a href="{{ url('event/create') }}" class="btn btn-primary">Create New Event</a>
</p>

{{ $event ->links()}}

@endsection
