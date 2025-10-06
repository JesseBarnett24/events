@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Edit Profile</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" class="card shadow-sm p-4">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Name:</label>
            <input id="name" type="text" name="name" class="form-control"
                   value="{{ old('name', $user->name) }}" required>
            @error('name') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email:</label>
            <input id="email" type="email" name="email" class="form-control"
                   value="{{ old('email', $user->email) }}" required>
            @error('email') <div class="text-danger mt-1">{{ $message }}</div> @enderror
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success">Update Profile</button>
            <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
