@extends('layouts.app')

@section('content')
<!-- Display user's booked events with options to view or cancel -->
<div class="container py-6">
    <h1 class="text-2xl font-bold mb-6">My Bookings</h1>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    @if($bookings->isEmpty())
        <p class="text-gray-600">You have not booked any events yet.</p>
        <a href="{{ url('/events') }}" class="inline-block mt-3 text-blue-600 hover:underline">
            Browse Events →
        </a>
    @else
        <!-- Display user's current bookings -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($bookings as $booking)
                <div class="bg-white rounded-lg shadow hover:shadow-md transition p-4 flex flex-col">
                    <h2 class="text-lg font-semibold mb-1">
                        <a href="{{ url('/events/' . $booking->event->id) }}" class="text-blue-600 hover:underline">
                            {{ $booking->event->title }}
                        </a>
                    </h2>
                    <p class="text-gray-600 text-sm mb-2">
                        {{ \Carbon\Carbon::parse($booking->event->starts_at)->format('d M Y, H:i') }}
                    </p>
                    <p class="text-gray-700 text-sm mb-2">{{ $booking->event->location }}</p>
                    <p class="text-xs text-gray-500 mb-3">
                        Organiser: {{ $booking->event->organiser->name }}
                    </p>

                    <!-- Display categories if event has any -->
                    @if($booking->event->categories->isNotEmpty())
                        <p class="text-xs text-gray-500 mb-3">
                            Categories:
                            @foreach($booking->event->categories as $category)
                                <span class="inline-block bg-blue-100 text-blue-600 rounded px-2 py-0.5 text-xs mr-1">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </p>
                    @endif

                    <!-- Booking actions: view or cancel -->
                    <div class="mt-auto flex justify-between items-center">
                        <a href="{{ url('/events/' . $booking->event->id) }}" 
                           class="text-sm text-blue-600 hover:underline">View Details</a>

                        <form method="POST" action="{{ url('/bookings/' . $booking->id . '/cancel') }}" 
                              onsubmit="return confirm('Cancel your booking for this event?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-500 hover:underline">Cancel</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Display pagination if booking list exceeds one page -->
        <div class="mt-6">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
