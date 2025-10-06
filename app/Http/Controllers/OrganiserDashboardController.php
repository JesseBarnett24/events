<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;

class OrganiserDashboardController extends Controller
{
    /**
     * Show organiser dashboard & report.
     */
    public function dashboard($id)
    {
        if (auth()->user()->role !== 'organiser' || auth()->id() != $id) {
            abort(403, 'Unauthorized access.');
        }
        
        $user = User::findOrFail($id);

        // Raw SQL query for the report (required by assignment)
        // Joins events and bookings, groups by event, and counts current bookings
        $reportRows = DB::select("
            SELECT 
                e.id,
                e.title,
                e.starts_at,
                e.location,
                e.capacity,
                COUNT(b.id) AS total_bookings,
                (e.capacity - COUNT(b.id)) AS remaining_spots
            FROM events e
            LEFT JOIN bookings b ON e.id = b.event_id
            WHERE e.organiser_id = ?
            GROUP BY e.id, e.title, e.starts_at, e.location, e.capacity
            ORDER BY e.starts_at ASC
        ", [$id]);

        // Convert to collection for easy use in the view
        $events = collect($reportRows);

        // Quick summary metrics
        $summary = [
            'total_events' => $events->count(),
            'total_bookings' => $events->sum('total_bookings'),
            'remaining_spots' => $events->sum('remaining_spots'),
        ];

        return view('organisers.dashboard', compact('user', 'events', 'summary'));
    }
}
