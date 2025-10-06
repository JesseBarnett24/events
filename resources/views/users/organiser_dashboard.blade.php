@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-3">Welcome, {{ $user->name }} (Organiser)</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <h3>Your Events</h3>

    @forelse($events as $event)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">
                <h5 class="card-title">{{ $event->title }}</h5>
                <p class="card-text">
                    <strong>Date:</strong> {{ \Carbon\Carbon::parse($event->starts_at)->format('d M Y, H:i') }}<br>
                    <strong>Location:</strong> {{ $event->location }}<br>
                    <strong>Capacity:</strong> {{ $event->capacity }}<br>
                    <strong>Current Bookings:</strong> {{ $event->bookings_count ?? $event->bookings->count() }}<br>
                    <strong>Remaining Spots:</strong>
                    {{ $event->capacity - ($event->bookings_count ?? $event->bookings->count()) }}<br>
                    <strong>Categories:</strong> {{ $event->categories->pluck('name')->join(', ') }}
                </p>
                <a href="{{ url('/events/' . $event->id . '/edit') }}" class="btn btn-sm btn-primary">Edit</a>
                <form action="{{ url('/events/' . $event->id) }}" method="POST" class="d-inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger"
                            onclick="return confirm('Delete this event?')">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <p>You have not created any events yet.</p>
    @endforelse

    <div class="mt-4">
        <a href="{{ url('/events/create') }}
