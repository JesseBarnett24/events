@extends('layouts.master')

@section('Title', 'Events')

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-2">
    Upcoming Events
</h1>

@if(session('success'))
    <div style="color: green">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div style="color: red">{{ session('error') }}</div>
@endif

<!-- Category selection form for filtering events -->
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

<div class="event-grid">
    @forelse ($events as $event)
        @include('events.partials.event_card', ['event' => $event])
    @empty
        <p>No upcoming events found.</p>
    @endforelse
</div>

<div class="pagination">
    {{ $events->withQueryString()->links() }}
</div>

<!-- Display create event button for organisers -->
@if(Auth::check() && Auth::user()->role === 'organiser')
    <p><a href="{{ url('/events/create') }}" class="inline-block px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md hover:bg-blue-200 transition">
    Create New Event</a></p>
@endif
@endsection
