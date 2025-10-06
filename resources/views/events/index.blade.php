@extends('layouts.master')

@section('Title', 'Events')

@section('content')
<h1>Upcoming Events</h1>

<!-- Flash Messages -->
@if(session('success'))
    <div style="color: green">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="color: red">{{ session('error') }}</div>
@endif

<!-- Category Filter -->
<form method="GET" action="{{ url('/events') }}" id="categoryFilterForm">
    <label for="category">Filter by Category:</label>
    <select name="category_id" id="category" onchange="document.getElementById('categoryFilterForm').submit();">
        <option value="all">All Categories</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</form>

<!-- Event List -->
<div class="event-grid">
    @forelse ($events as $event)
        @include('events.partials.event_card', ['event' => $event])
    @empty
        <p>No upcoming events found.</p>
    @endforelse
</div>

<!-- Pagination -->
<div class="pagination">
    {{ $events->withQueryString()->links() }}
</div>

<!-- Add Event (for Organisers) -->
@if(Auth::check() && Auth::user()->role === 'organiser')
    <p><a href="{{ url('/events/create') }}">➕ Create New Event</a></p>
@endif
@endsection
