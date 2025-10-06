@extends('layouts.master')

@section('Title', $event->title)

@section('content')
    <h1>{{ $event->title }}</h1>

    {{-- ✅ Flash Messages --}}
    @if (session('success'))
        <div style="color: green; margin-bottom: 10px;">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div style="color: red; margin-bottom: 10px;">{{ session('error') }}</div>
    @endif

    {{-- ✅ Event Details --}}
    <div style="margin-bottom: 20px;">
        <p><strong>Description:</strong> {{ $event->description ?? 'No description provided.' }}</p>
        <p><strong>Date & Time:</strong> {{ \Carbon\Carbon::parse($event->starts_at)->format('d M Y, h:i A') }}</p>
        <p><strong>Location:</strong> {{ $event->location }}</p>
        <p><strong>Organiser:</strong> {{ $event->organiser->name ?? 'Unknown' }}</p>
        <p><strong>Capacity:</strong> {{ $event->capacity }}</p>
        <p><strong>Bookings:</strong> {{ $event->bookings->count() }}</p>
        <p><strong>Remaining Spots:</strong> {{ $remaining_spots }}</p>
    </div>

    {{-- ✅ Categories --}}
    <p>
        <strong>Categories:</strong>
        @foreach ($event->categories as $category)
            <span style="border: 1px solid #999; padding: 3px 6px; border-radius: 4px; margin-right: 5px;">
                {{ $category->name }}
            </span>
        @endforeach
    </p>

    <hr style="margin: 20px 0;">

    {{-- ✅ User Actions --}}
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
        {{-- 🧍 Attendee Actions --}}
        @if ($user->role === 'attendee')
            @if ($remaining_spots > 0 && !$alreadyBooked)
                <form method="POST" action="{{ url('/bookings') }}" style="margin-top: 10px;">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <button type="submit" style="padding: 8px 15px;">🎟️ Book Now</button>
                </form>
            @elseif ($alreadyBooked)
                <p style="color: gray;">You have already booked this event.</p>
            @elseif ($remaining_spots <= 0)
                <p style="color: red;">This event is full.</p>
            @endif
        @endif

        {{-- 🧑‍💼 Organiser Actions --}}
        @if ($user->role === 'organiser' && $user->id === $event->organiser_id)
            <div style="margin-top: 20px;">
                <a href="{{ url('/events/' . $event->id . '/edit') }}" style="margin-right: 10px;">✏️ Edit Event</a>

                <form method="POST" action="{{ url('/events/' . $event->id) }}" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color: red;"
                        onclick="return confirm('Are you sure you want to delete this event?')">🗑 Delete</button>
                </form>
            </div>
        @endif
    @else
        {{-- 🕶 Guest Users --}}
        <p><a href="{{ url('/login') }}">Log in</a> to book this event.</p>
    @endif

    <hr style="margin: 20px 0;">

    <a href="{{ url('/events') }}">← Back to Events</a>
@endsection
