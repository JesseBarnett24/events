@extends('layouts.master')

@section('Title', $event->title)

@section('content')
<h1 class="text-3xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-2">{{ $event->title }}</h1>

<!-- Display flash messages after actions -->
@if (session('success'))
    <div style="color: green; margin-bottom: 10px;">{{ session('success') }}</div>
@endif
@if (session('error'))
    <div style="color: red; margin-bottom: 10px;">{{ session('error') }}</div>
@endif

<!-- Event details section -->
<div style="margin-bottom: 20px;">
    <p><strong>Description:</strong> {{ $event->description ?? 'No description provided.' }}</p>
    <p><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($event->starts_at)->format('d M Y, h:i A') }}</p>
    <p><strong>Location:</strong> {{ $event->location }}</p>
    <p><strong>Organiser:</strong> {{ $event->organiser->name ?? 'Unknown' }}</p>
    <p><strong>Capacity:</strong> {{ $event->capacity }}</p>
    <p><strong>Bookings:</strong> {{ $event->bookings->count() }}</p>
    <p><strong>Remaining Spots:</strong> {{ $remaining_spots }}</p>
</div>

<!-- Event categories -->
<p>
    <strong>Categories:</strong>
    @foreach ($event->categories as $category)
        <span class="inline-block px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md hover:bg-blue-200 transition">
            {{ $category->name }}
        </span>
    @endforeach
</p>

<hr style="margin: 20px 0;">

<!-- Determine user role and booking state -->
@php
    use Illuminate\Support\Facades\Auth;
    $user = Auth::user();
    $alreadyBooked = false;
    if ($user && $user->role === 'attendee') {
        $alreadyBooked = \App\Models\Booking::where('user_id', $user->id)
                            ->where('event_id', $event->id)
                            ->exists();
    }
@endphp

@if (Auth::check())
    <!-- Attendee booking actions -->
    @if ($user->role === 'attendee')
        @if ($remaining_spots > 0 && !$alreadyBooked)
            <form method="POST" action="{{ url('/bookings') }}" style="margin-top: 10px;">
                @csrf
                <input type="hidden" name="event_id" value="{{ $event->id }}">
                <button type="submit" class="inline-block px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md hover:bg-blue-200 transition">Book Now</button>
            </form>
        @elseif ($alreadyBooked)
            <p style="color: gray;">You have already booked this event.</p>
        @elseif ($remaining_spots <= 0)
            <p style="color: red;">This event is full.</p>
        @endif
    @endif

    <!-- Organiser edit and delete options -->
    @if ($user->role === 'organiser' && $user->id === $event->organiser_id)
        <div style="margin-top: 20px;">
            <a href="{{ url('/events/' . $event->id . '/edit') }}" class="inline-block px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md hover:bg-blue-200 transition">Edit Event</a>

            <form method="POST" action="{{ url('/events/' . $event->id) }}" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-block px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md hover:bg-blue-200 transition"
                    onclick="return confirm('Are you sure you want to delete this event?')">Delete</button>
            </form>
        </div>
    @endif
@else
    <p><a href="{{ url('/login') }}">Log in</a> to book this event.</p>
@endif

<hr style="margin: 20px 0;">

<a href="{{ url('/events') }}">← Back to Events</a>
@endsection
