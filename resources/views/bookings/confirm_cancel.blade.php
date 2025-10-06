@extends('layouts.app')

@section('content')
<!-- Display booking status message based on session value -->
<div class="container py-6 text-center">
    <div class="bg-white shadow-md rounded-lg p-6 mx-auto max-w-lg">
        @if(session('status') === 'cancelled')
            <h1 class="text-xl font-bold text-red-600 mb-3">Booking Cancelled</h1>
            <p class="text-gray-700 mb-4">
                Your booking for <strong>{{ $event->title ?? 'this event' }}</strong> has been cancelled.
            </p>
        @elseif(session('status') === 'full')
            <h1 class="text-xl font-bold text-red-600 mb-3">Event Full</h1>
            <p class="text-gray-700 mb-4">
                Sorry, this event has reached its capacity. Please try another event or check back later.
            </p>
        @elseif(session('status') === 'duplicate')
            <h1 class="text-xl font-bold text-yellow-600 mb-3">Already Booked</h1>
            <p class="text-gray-700 mb-4">
                You are already booked for this event. You can view it in <a href="{{ url('/bookings/mine') }}" class="text-blue-600 hover:underline">My Bookings</a>.
            </p>
        @else
            <h1 class="text-xl font-bold text-green-600 mb-3">Booking Successful</h1>
            <p class="text-gray-700 mb-4">
                Your booking has been confirmed for <strong>{{ $event->title ?? 'this event' }}</strong>!
            </p>
        @endif

        <!-- Link back to user's bookings page -->
        <a href="{{ url('/bookings/mine') }}" 
           class="inline-block mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
           Back to My Bookings
        </a>
    </div>
</div>
@endsection
