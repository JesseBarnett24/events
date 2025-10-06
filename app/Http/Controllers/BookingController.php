<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Models\Event;
use App\Models\Booking;

class BookingController extends Controller implements HasMiddleware
{
    // Apply authentication middleware to all routes in this controller
    public static function middleware(): array
    {
        return [ new Middleware('auth') ];
    }

    // Display all bookings for the logged-in attendee
    // @return \Illuminate\View\View
    public function index()
    {
        if (Auth::user()->role !== 'attendee') {
            abort(403, 'Only attendees can view bookings.');
        }

        $bookings = Booking::with(['event.organiser', 'event.categories'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(8);

        return view('bookings.my_bookings', compact('bookings'));
    }

    // Store a new booking for an event after validating capacity and duplicates
    // @param Request $request
    // @return \Illuminate\Http\RedirectResponse
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'attendee') {
            abort(403, 'Only attendees can book events.');
        }

        $request->validate([
            'event_id' => 'required|exists:events,id',
        ]);

        $event = Event::withCount('bookings')->findOrFail($request->event_id);

        // Check if event has reached capacity
        if ($event->bookings_count >= $event->capacity) {
            return back()->with('error', 'This event is already full.');
        }

        // Prevent user from booking the same event multiple times
        if ($event->bookings()->where('user_id', Auth::id())->exists()) {
            return back()->with('error', 'You have already booked this event.');
        }

        Booking::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
        ]);

        return redirect('/bookings/mine')->with('success', 'Your booking has been confirmed!');
    }

    // Cancel and delete a specific booking made by the logged-in attendee
    // @param int $id
    // @return \Illuminate\Http\RedirectResponse
    public function destroy($id)
    {
        if (Auth::user()->role !== 'attendee') {
            abort(403, 'Only attendees can cancel bookings.');
        }

        $booking = Booking::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $booking->delete();

        return redirect('/bookings/mine')->with('success', 'Booking cancelled successfully.');
    }
}
