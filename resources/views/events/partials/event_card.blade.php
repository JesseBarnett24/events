<div class="event-card" style="border:1px solid #ccc; padding:10px; margin:10px;">
    <h3>
        <a href="{{ url('/events/' . $event->id) }}">{{ $event->title }}</a>
    </h3>
    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($event->starts_at)->format('d M Y, h:i A') }}</p>
    <p><strong>Location:</strong> {{ $event->location }}</p>
    <p><strong>Organiser:</strong> {{ $event->organiser->name ?? 'Unknown' }}</p>

    <p>
        <strong>Categories:</strong>
        @foreach($event->categories as $category)
            <span class="inline-block px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md hover:bg-blue-200 transition">{{ $category->name }}</span>
        @endforeach
    </p>
</div>
