<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class OrganiserDashboardController extends Controller
{
    public function dashboard($id)
    {
        if (auth()->user()->role !== 'organiser' || auth()->id() != $id) {
            abort(403, 'Unauthorized access.');
        }

        $user = User::findOrFail($id);

        // Each event capacity counted ONCE per organiser+category pair
        $rawStats = DB::select("
            SELECT 
                c.name AS category_name,
                COUNT(DISTINCT e.id) AS event_count,
                COUNT(b.id) AS total_bookings,
                SUM(DISTINCT e.capacity) AS total_capacity
            FROM 
                categories AS c,
                category_event AS ce,
                events AS e
                LEFT JOIN bookings AS b ON b.event_id = e.id
            WHERE 
                c.id = ce.category_id
                AND ce.event_id = e.id
                AND e.organiser_id = ?
            GROUP BY 
                c.name
            ORDER BY 
                total_bookings DESC;
        ", [$id]);
    

        //Compute occupancy % safely in PHP
        $categoryStats = collect($rawStats)->map(function ($cat) {
            $cat->avg_occupancy = $cat->total_capacity > 0
                ? round(($cat->total_bookings / $cat->total_capacity) * 100, 1)
                : 0;
            return $cat;
        });

        //Event report (unchanged)
        $reportRows = DB::select("
            SELECT 
                e.id,
                e.title,
                e.starts_at,
                e.location,
                e.capacity,
                COUNT(b.id) AS total_bookings,
                (e.capacity - COUNT(b.id)) AS remaining_spots
            FROM 
                events AS e
                LEFT JOIN bookings AS b ON e.id = b.event_id
            WHERE 
                e.organiser_id = ?
            GROUP BY 
                e.id, 
                e.title, 
                e.starts_at, 
                e.location, 
                e.capacity
            ORDER BY 
                e.starts_at ASC;
        ", [$id]);

        $events = collect($reportRows);

        $summary = [
            'total_events' => $events->count(),
            'total_bookings' => $events->sum('total_bookings'),
            'remaining_spots' => $events->sum('remaining_spots'),
        ];

        return view('organisers.dashboard', compact('user', 'events', 'summary', 'categoryStats'));
    }
}
