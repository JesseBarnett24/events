@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Organiser Dashboard</h1>

    <div class="alert alert-info">
        <strong>Raw SQL Report Generated:</strong> Uses <code>DB::select()</code> to aggregate bookings and remaining spots.
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Events</h6>
                    <h3>{{ $summary['total_events'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Bookings</h6>
                    <h3>{{ $summary['total_bookings'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Remaining Spots</h6>
                    <h3>{{ $summary['remaining_spots'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Report -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Event Report for {{ $user->name }}</h5>
        </div>
        <div class="card-body p-0">
            @if ($events->isEmpty())
                <div class="p-4">
                    <p class="text-muted mb-0">You have not created any events yet.</p>
                    <a href="{{ url('/events/create') }}" class="btn btn-success mt-3">Create Event</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Date & Time</th>
                                <th>Location</th>
                                <th>Capacity</th>
                                <th>Bookings</th>
                                <th>Remaining</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($events as $event)
                                <tr>
                                    <td>{{ $event->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->starts_at)->format('d M Y, H:i') }}</td>
                                    <td>{{ $event->location }}</td>
                                    <td>{{ $event->capacity }}</td>
                                    <td>{{ $event->total_bookings }}</td>
                                    <td>{{ $event->remaining_spots }}</td>
                                    <td class="text-end">
                                        <a href="{{ url('/events/' . $event->id) }}" class="btn btn-sm btn-outline-secondary">View</a>
                                        <a href="{{ url('/events/' . $event->id . '/edit') }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ url('/events/' . $event->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this event?')" type="submit">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
