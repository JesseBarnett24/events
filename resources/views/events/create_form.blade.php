@extends('layouts.master')

@section('Title', 'Create Event')

@section('content')
<h1 class="text-2xl font-semibold mb-6 text-center">Create Event</h1>

@if ($errors->any())
    <div class="bg-red-100 text-red-700 p-4 rounded mb-6">
        <ul class="list-disc ml-6">
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ url('/events') }}" class="bg-white shadow-md rounded-lg p-6 max-w-2xl mx-auto">
    @csrf

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Title</label>
        <input type="text" name="title" value="{{ old('title') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Description</label>
        <textarea name="description" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Date & Time</label>
        <input type="datetime-local" name="starts_at" value="{{ old('starts_at') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Location</label>
        <input type="text" name="location" value="{{ old('location') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="mb-4">
        <label class="block text-gray-700 font-medium mb-1">Capacity</label>
        <input type="number" name="capacity" min="1" max="1000" value="{{ old('capacity') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
    </div>

    <div class="mb-6">
        <label class="block text-gray-700 font-medium mb-2">Categories</label>
        <div class="flex flex-wrap gap-2">
            @foreach ($categories as $category)
                <label class="text-sm text-gray-700">
                    <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                           {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                    {{ $category->name }}
                </label>
            @endforeach
        </div>
    </div>

    <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 w-full">
        Create Event
    </button>
</form>

<div class="text-center mt-6">
    <a href="{{ url('/events') }}" class="text-blue-600 hover:underline">← Back to Events</a>
</div>
@endsection
