<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Event;
use App\Models\Category;
use App\Models\Booking;
use Illuminate\Support\Facades\View;

class EventController extends Controller
{
    // Display a list of upcoming events with optional category filtering
    // @param Request $request
    // @return \Illuminate\View\View
    public function index(Request $request)
    {
        $query = Event::with(['categories', 'organiser'])
            ->where('starts_at', '>=', now()) // Only include future events
            ->orderBy('starts_at', 'asc');

        // Apply category filter if provided
        if ($request->has('category_id') && $request->category_id !== 'all') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        $events = $query->paginate(8);
        $categories = Category::all();

        return view('events.index', compact('events', 'categories'));
    }

    // Filter events dynamically based on multiple categories or search input
    // @param Request $request
    // @return \Illuminate\Http\JsonResponse
    public function filter(Request $request)
    {
        $categories = $request->query('categories', []);
        $search = $request->query('search', '');
    
        $query = Event::with(['categories', 'organiser'])
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at', 'asc');
    
        // Apply multi-category filtering (matches any of the selected categories)
        if (!empty($categories)) {
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('categories.id', $categories);
            });
        }
    
        // Search across title, description, organiser, and event date
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                  ->orWhere('description', 'LIKE', "%$search%")
                  ->orWhere('location', 'LIKE', "%$search%")
                  ->orWhereHas('organiser', function ($org) use ($search) {
                      $org->where('name', 'LIKE', "%$search%");
                  });
    
                // Allow date-based searching if input resembles a date
                if (strtotime($search)) {
                    $date = date('Y-m-d', strtotime($search));
                    $q->orWhereDate('starts_at', $date);
                }
            });
        }
    
        $events = $query->paginate(8);
    
        $html = view('events.partials.event_grid', compact('events'))->render();
        $pagination = $events->withQueryString()->links()->render();
    
        return response()->json([
            'html' => $html,
            'pagination' => $pagination,
        ]);
    }
    
    // Display details for a single event, including booking and edit permissions
    // @param int $id
    // @return \Illuminate\View\View
    public function show($id)
    {
        $event = Event::with(['categories', 'organiser', 'bookings'])->findOrFail($id);

        $remaining_spots = $event->capacity - $event->bookings->count();
        $user = Auth::user();

        $user_can_book = false;
        $user_can_edit = false;

        if (Auth::check()) {
            // Check if attendee can book this event
            if ($user->role === 'attendee' && $remaining_spots > 0 && $event->starts_at > now()) {
                $alreadyBooked = Booking::where('user_id', $user->id)
                    ->where('event_id', $event->id)
                    ->exists();

                $user_can_book = !$alreadyBooked;
            }

            // Check if organiser owns the event and can edit it
            if ($user->role === 'organiser' && $event->organiser_id === $user->id) {
                $user_can_edit = true;
            }
        }

        return view('events.show', compact('event', 'remaining_spots', 'user_can_book', 'user_can_edit'));
    }

    // Show the event creation form (organiser only)
    // @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    public function create()
    {
        if (!Auth::check() || Auth::user()->role !== 'organiser') {
            return redirect('/events')->with('error', 'You must be an organiser to create events.');
        }

        $categories = Category::all();
        return view('events.create_form', compact('categories'));
    }

    // Store a new event after validation (organiser only)
    // @param Request $request
    // @return \Illuminate\Http\RedirectResponse
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

        // Attach selected categories to the new event
        $event->categories()->attach($validated['categories']);

        return redirect("/events/{$event->id}")
            ->with('success', 'Event created successfully.');
    }

    // Display the form for editing an existing event (organiser only)
    // @param int $id
    // @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
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

    // Update an event’s details after validation (organiser only)
    // @param Request $request
    // @param int $id
    // @return \Illuminate\Http\RedirectResponse
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

    // Delete an event only if there are no existing bookings
    // @param int $id
    // @return \Illuminate\Http\RedirectResponse
    public function destroy($id)
    {
        $event = Event::with('bookings')->findOrFail($id);

        if (!Auth::check() || Auth::user()->role !== 'organiser' || $event->organiser_id !== Auth::id()) {
            return redirect('/events')->with('error', 'Unauthorised action.');
        }

        // Prevent deletion if event has bookings
        if ($event->bookings->count() > 0) {
            return redirect("/events/{$event->id}")
                ->with('error', 'Cannot delete event with existing bookings.');
        }

        $event->categories()->detach();
        $event->delete();

        return redirect('/events')->with('success', 'Event deleted successfully.');
    }

    // Display organiser dashboard with a raw SQL summary report of their events
    // @param int $id
    // @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
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
