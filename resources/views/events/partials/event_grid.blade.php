@foreach ($events as $event)
    <div class="event-card border p-3 mb-3 rounded shadow-sm">
        <h5 class="fw-bold">{{ $event->title }}</h5>
        <p class="text-muted mb-1">{{ \Carbon\Carbon::parse($event->starts_at)->format('d M Y, H:i') }}</p>
        <p class="mb-1">{{ $event->location }}</p>
        <p class="small text-secondary">
            Categories: {{ $event->categories->pluck('name')->join(', ') }}
        </p>
        <a href="{{ url('/events/' . $event->id) }}" class="btn btn-sm btn-outline-primary">
            View Details
        </a>
    </div>
@endforeach
