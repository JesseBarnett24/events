@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Welcome, {{ $user->name }} (Attendee)</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h3>Your Bookings</h3>

    @forelse($bookings as $booking)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ optional($booking->event)->title ?? 'Event Deleted' }}</h5>
                @if ($booking->event)
                    <p class="card-text">
                        <strong>Date:</strong> {{ \Carbon\Carbon::parse($booking->event->starts_at)->format('d M Y, H:i') }}<br>
                        <strong>Location:</strong> {{ $booking->event->location }}<br>
                        <strong>Categories:</strong> {{ $booking->event->categories->pluck('name')->join(', ') }}
                    </p>
                    <form action="{{ url('/bookings/' . $booking->id . '/cancel') }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Cancel this booking?')">Cancel Booking</button>
                    </form>
                @else
                    <p class="text-muted">This event no longer exists.</p>
                @endif
            </div>
        </div>
    @empty
        <p>You have not booked any events yet.</p>
    @endforelse

    <div class="mt-4">
        <a href="{{ url('/events') }}" class="btn btn-outline-primary">Browse Upcoming Events</a>
    </div>
</div>
@endsection
