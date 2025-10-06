@extends('layouts.app')

@section('content')
<!-- Display user's booked events with options to view or cancel -->
<div style="padding: 20px;">
    <h1>My Bookings</h1>

    @if(session('success'))
        <div style="background-color:#d1e7dd; color:#0f5132; border:1px solid #badbcc; padding:10px; margin-bottom:15px;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background-color:#f8d7da; color:#842029; border:1px solid #f5c2c7; padding:10px; margin-bottom:15px;">
            {{ session('error') }}
        </div>
    @endif

    @if($bookings->isEmpty())
        <p>You have not booked any events yet.</p>
        <a href="{{ url('/events') }}">Browse Events →</a>
    @else
        <!-- Display user's current bookings -->
        <div style="display:flex; flex-wrap:wrap; gap:20px;">
            @foreach ($bookings as $booking)
                <div style="border:1px solid #ccc; padding:15px; width:300px;">
                    <h2>
                        <a href="{{ url('/events/' . $booking->event->id) }}">
                            {{ $booking->event->title }}
                        </a>
                    </h2>
                    <p>{{ \Carbon\Carbon::parse($booking->event->starts_at)->format('d M Y, H:i') }}</p>
                    <p>{{ $booking->event->location }}</p>
                    <p>Organiser: {{ $booking->event->organiser->name }}</p>

                    <!-- Display categories if event has any -->
                    @if($booking->event->categories->isNotEmpty())
                        <p>
                            Categories:
                            @foreach($booking->event->categories as $category)
                                <span style="background-color:#e0e7ff; color:#1e40af; padding:2px 5px; border-radius:3px; font-size:12px; margin-right:3px;">
                                    {{ $category->name }}
                                </span>
                            @endforeach
                        </p>
                    @endif

                    <!-- Booking actions: view or cancel -->
                    <div style="margin-top:10px; display:flex; justify-content:space-between; align-items:center;">
                        <a href="{{ url('/events/' . $booking->event->id) }}">View Details</a>

                        <form method="POST" action="{{ url('/bookings/' . $booking->id . '/cancel') }}" 
                              onsubmit="return confirm('Cancel your booking for this event?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="color:red; background:none; border:none; cursor:pointer;">
                                Cancel
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Display pagination if booking list exceeds one page -->
        <div style="margin-top:20px;">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection
