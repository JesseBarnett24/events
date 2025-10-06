<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Category;
use App\Models\Booking;

class EventController extends Controller
{
    /**
     * Display a listing of all upcoming events (public).
     * Supports optional category filtering via ?category_id=
     */
    public function index(Request $request)
    {
        $query = Event::with(['categories', 'organiser'])
            ->where('starts_at', '>=', now()) // Only future events
            ->orderBy('starts_at', 'asc');

        // Optional category filter
        if ($request->has('category_id') && $request->category_id !== 'all') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $events = $query->paginate(8);
        $categories = Category::all();

        return view('events.index', compact('events', 'categories'));
    }

    /**
     * Display details for a specific event.
     */
    public function show($id)
    {
        $event = Event::with(['categories', 'organiser', 'bookings'])->findOrFail($id);

        $remaining_spots = $event->capacity - $event->bookings->count();
        $user = Auth::user();

        $user_can_book = false;
        $user_can_edit = false;

        if (Auth::check()) {
            // Attendee can book if not full, future event, and hasn’t already booked
            if ($user->role === 'attendee' && $remaining_spots > 0 && $event->starts_at > now()) {
                $alreadyBooked = Booking::where('user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->exists();

                $user_can_book = !$alreadyBooked;
            }

            // Organiser can edit/delete their own events
            if ($user->role === 'organiser' && $event->organiser_id === $user->id) {
                $user_can_edit = true;
            }
        }

        return view('events.show', compact('event', 'remaining_spots', 'user_can_book', 'user_can_edit'));
    }

    /**
     * Show the form for creating a new event (organiser only).
     */
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'organiser') {
            return redirect('/events')->with('error', 'You must be an organiser to create events.');
        }

        $categories = Category::all();
        return view('events.create_form', compact('categories'));
    }

    /**
     * Store a newly created event (organiser only).
     */
    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'organiser') {
            return redirect('/events')->with('error', 'Unauthorised action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'starts_at' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:1000',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        $event = new Event($validated);
        $event->organiser_id = Auth::id();
        $event->save();

        // Attach categories
        $event->categories()->attach($validated['categories']);

        return redirect("/events/{$event->id}")
            ->with('success', 'Event created successfully.');
    }

    /**
     * Show the form for editing an existing event.
     */
    public function edit($id)
    {
        $event = Event::findOrFail($id);

        if (!Auth::check() || Auth::user()->role !== 'organiser' || $event->organiser_id !== Auth::id()) {
            return redirect('/events')->with('error', 'You are not authorised to edit this event.');
        }

        $categories = Category::all();
        $selected_categories = $event->categories->pluck('id')->toArray();

        return view('events.edit_form', compact('event', 'categories', 'selected_categories'));
    }

    /**
     * Update the specified event.
     */
    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);

        if (!Auth::check() || Auth::user()->role !== 'organiser' || $event->organiser_id !== Auth::id()) {
            return redirect('/events')->with('error', 'Unauthorised action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'starts_at' => 'required|date|after:now',
            'location' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1|max:1000',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
        ]);

        $event->update($validated);
        $event->categories()->sync($validated['categories']);

        return redirect("/events/{$event->id}")
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event (only if it has no bookings).
     */
    public function destroy($id)
    {
        $event = Event::with('bookings')->findOrFail($id);

        if (!Auth::check() || Auth::user()->role !== 'organiser' || $event->organiser_id !== Auth::id()) {
            return redirect('/events')->with('error', 'Unauthorised action.');
        }

        if ($event->bookings->count() > 0) {
            return redirect("/events/{$event->id}")
                ->with('error', 'Cannot delete event with existing bookings.');
        }

        $event->categories()->detach();
        $event->delete();

        return redirect('/events')->with('success', 'Event deleted successfully.');
    }

    /**
     * Organiser Dashboard — Raw SQL report.
     */
    public function dashboard($id)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'organiser' || $user->id != $id) {
            return redirect('/events')->with('error', 'Access denied.');
        }

        $report = DB::select("
            SELECT e.id, e.title, e.starts_at, e.capacity,
                   COUNT(b.id) AS total_bookings,
                   (e.capacity - COUNT(b.id)) AS remaining_spots
            FROM events e
            LEFT JOIN bookings b ON e.id = b.event_id
            WHERE e.organiser_id = ?
            GROUP BY e.id, e.title, e.starts_at, e.capacity
            ORDER BY e.starts_at ASC
        ", [$id]);

        return view('organisers.dashboard', compact('report', 'user'));
    }


}
