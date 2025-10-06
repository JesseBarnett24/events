@extends('layouts.app')

@section('content')
<!-- Display event edit form for organisers -->
<div class="container py-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-6 border-b border-gray-200 pb-2">Edit Event</h1>

    <!-- Event edit form -->
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
                <input type="datetime-local" name="starts_at"
                    value="{{ old('starts_at', \Carbon\Carbon::parse($event->starts_at)->format('Y-m-d\TH:i')) }}"
                    class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-medium">Location</label>
                <input type="text" name="location" value="{{ old('location', $event->location) }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="mt-4">
            <label class="block font-medium">Capacity</label>
            <input type="number" name="capacity" value="{{ old('capacity', $event->capacity) }}" min="1" max="1000" class="w-full border rounded px-3 py-2">
        </div>

        <div class="mt-4">
            <label class="block font-medium mb-2">Categories</label>
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                @foreach ($categories as $category)
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                            {{ in_array($category->id, old('categories', $event->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                            class="rounded text-blue-600 focus:ring-blue-500">
                        <span>{{ $category->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('categories') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Update Event
            </button>
        </div>
    </form>
</div>
@endsection
