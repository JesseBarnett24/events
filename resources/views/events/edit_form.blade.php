@extends('layouts.app')

@section('content')
<div class="container py-6">
    <h1 class="text-2xl font-bold mb-4">Edit Event</h1>

    <form method="POST" action="{{ url('/events/' . $event->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="block font-medium">Title</label>
            <input type="text" name="title" value="{{ old('title', $event->title) }}" class="w-full border rounded px-3 py-2">
            @error('title') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
        </div>

        <div class="mb-3">
            <label class="block font-medium">Description</label>
            <textarea name="description" rows="4" class="w-full border rounded px-3 py-2">{{ old('description', $event->description) }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-medium">Date & Time</label>
                <input type="datetime-local" name="starts_at" value="{{ old('starts_at', \Carbon\Carbon::parse($event->starts_at)->format('Y-m-d\TH:i')) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium">Location</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
            <div>
                <label class="block font-medium">Capacity</label>
                <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium">Categories</label>
                <select name="categories[]" multiple class="w-full border rounded px-3 py-2">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ $event->categories->contains($category->id) ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mt-3">
            <label class="block font-medium">Image (optional)</label>
            <input type="file" name="image">
            @if($event->image)
                <p class="text-sm text-gray-600 mt-1">Current image: {{ $event->image }}</p>
            @endif
        </div>

        <div class="mt-5">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Update Event
            </button>
        </div>
    </form>
</div>
@endsection
