@extends('layouts.app')

@section('content')
<!-- Display organiser dashboard with event analytics and reports -->
<div class="container py-4">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-2">
        Organiser Dashboard
    </h1>

    <!-- Category popularity bar chart -->
    <div class="card shadow-sm border-0 mt-5">
        <div class="card-header bg-indigo-600 text-white">
            <h5 class="mb-0">Category Popularity — Average Occupancy</h5>
        </div>
        <div class="card-body" style="position: relative;">
            <div id="chartContainer" style="width: 100%; height: auto;">
                <canvas id="categoryPopularityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Chart.js configuration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = {!! json_encode($categoryStats->pluck('category_name')) !!};
            const values = {!! json_encode($categoryStats->pluck('avg_occupancy')) !!};

            const canvas = document.getElementById('categoryPopularityChart');
            const ctx = canvas.getContext('2d');

            // Calculate dynamic height for chart
            const baseHeightPerBar = 60;
            const minHeight = 250;
            const dynamicHeight = Math.max(labels.length * baseHeightPerBar, minHeight);
            canvas.height = dynamicHeight;

            // Set up y-axis maximum value rounded to nearest 10
            const maxValue = Math.max(...values, 10);
            const yMax = Math.ceil(maxValue / 10) * 10;

            // Create gradient for bar color
            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.8)');
            gradient.addColorStop(1, 'rgba(165, 180, 252, 0.4)');

            const data = {
                labels: labels,
                datasets: [{
                    label: 'Average Occupancy (%)',
                    data: values,
                    backgroundColor: gradient,
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    barThickness: 40,
                }]
            };

            const options = {
                responsive: true,
                maintainAspectRatio: false,
                layout: { padding: { top: 40, bottom: 10 } },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: yMax,
                        ticks: {
                            stepSize: yMax <= 50 ? 5 : 10,
                            callback: value => value + '%'
                        },
                        grid: { color: 'rgba(0,0,0,0.05)' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#555', font: { weight: '500' } }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(99,102,241,0.9)',
                        callbacks: {
                            label: context => context.parsed.y.toFixed(1) + '% occupancy'
                        }
                    },
                    datalabels: {
                        clip: false,
                        clamp: true,
                        color: '#333',
                        anchor: 'end',
                        align: 'end',
                        formatter: value => value.toFixed(1) + '%',
                        font: { weight: '600' }
                    }
                }
            };

            Chart.register(ChartDataLabels);

            new Chart(ctx, {
                type: 'bar',
                data: data,
                options: options,
                plugins: [ChartDataLabels]
            });
        });
    </script>

    <!-- Summary cards displaying organiser metrics -->
    <div class="flex flex-wrap justify-start gap-4 mt-8">
        <div class="bg-white shadow-sm border rounded-lg w-48 text-left p-4">
            <h6 class="text-gray-500 text-sm font-medium">Total Events</h6>
            <h3 class="text-2xl font-bold text-gray-800">{{ $summary['total_events'] ?? 0 }}</h3>
        </div>

        <div class="bg-white shadow-sm border rounded-lg w-48 text-left p-4">
            <h6 class="text-gray-500 text-sm font-medium">Total Bookings</h6>
            <h3 class="text-2xl font-bold text-gray-800">{{ $summary['total_bookings'] ?? 0 }}</h3>
        </div>

        <div class="bg-white shadow-sm border rounded-lg w-48 text-left p-4">
            <h6 class="text-gray-500 text-sm font-medium">Remaining Spots</h6>
            <h3 class="text-2xl font-bold text-gray-800">{{ $summary['remaining_spots'] ?? 0 }}</h3>
        </div>
    </div>

    <!-- Event report table -->
    <div class="card shadow-sm border-0 mt-8">
        <div class="card-header bg-indigo-600 text-white py-3 px-4">
            <h5 class="mb-0 text-lg font-semibold tracking-wide">
                Event Report — {{ $user->name }}
            </h5>
        </div>

        <div class="card-body p-0">
            @if ($events->isEmpty())
                <div class="p-5 text-center text-gray-600">
                    <p class="mb-3">You have not created any events yet.</p>
                    <a href="{{ url('/events/create') }}" 
                    class="btn btn-success px-4 py-2 text-white rounded shadow hover:bg-green-700 transition">
                    + Create Event
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0 border-t border-gray-200">
                        <thead class="bg-gray-100 text-gray-700 text-sm uppercase">
                            <tr class="border-b border-gray-300">
                                <th class="px-4 py-3">Title</th>
                                <th class="px-4 py-3">Date & Time</th>
                                <th class="px-4 py-3">Location</th>
                                <th class="px-4 py-3 text-center">Capacity</th>
                                <th class="px-4 py-3 text-center">Bookings</th>
                                <th class="px-4 py-3 text-center">Remaining</th>
                                <th class="px-4 py-3 text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach ($events as $event)
                                <tr class="hover:bg-indigo-50 transition duration-100">
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $event->title }}</td>
                                    <td class="px-4 py-3 text-gray-700">
                                        {{ \Carbon\Carbon::parse($event->starts_at)->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $event->location }}</td>
                                    <td class="px-4 py-3 text-center text-gray-700">{{ $event->capacity }}</td>
                                    <td class="px-4 py-3 text-center font-semibold text-blue-600">
                                        {{ $event->total_bookings }}
                                    </td>
                                    <td class="px-4 py-3 text-center font-semibold text-green-600">
                                        {{ $event->remaining_spots }}
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="flex justify-end gap-2">
                                            <!-- View event -->
                                            <a href="{{ url('/events/' . $event->id) }}"
                                            class="px-3 py-1.5 bg-blue-100 text-blue-700 text-sm rounded-md hover:bg-blue-200 transition">
                                            View
                                            </a>
                                            <!-- Edit event -->
                                            <a href="{{ url('/events/' . $event->id . '/edit') }}"
                                            class="px-3 py-1.5 bg-yellow-100 text-yellow-700 text-sm rounded-md hover:bg-yellow-200 transition">
                                            Edit
                                            </a>
                                            <!-- Delete event -->
                                            <form action="{{ url('/events/' . $event->id) }}" method="POST"
                                                onsubmit="return confirm('Delete this event?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="px-3 py-1.5 bg-red-100 text-red-700 text-sm rounded-md hover:bg-red-200 transition">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
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
