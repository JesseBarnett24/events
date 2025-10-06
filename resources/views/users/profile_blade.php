@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">{{ $user->name }}'s Profile</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @elseif (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <p><strong>Email:</strong> {{ $user->email }}</p>
            <p><strong>Role:</strong> {{ ucfirst($user->role) }}</p>
        </div>
    </div>

    <a href="{{ route('profile.edit') }}" class="btn btn-primary">Edit Profile</a>

    @if($user->role === 'organiser')
        <a href="{{ route('organiser.dashboard', $user->id) }}" class="btn btn-outline-secondary ms-2">
            View Dashboard
        </a>
    @else
        <a href="{{ url('/bookings/mine') }}" class="btn btn-outline-secondary ms-2">
            View My Bookings
        </a>
    @endif
</div>
@endsection
