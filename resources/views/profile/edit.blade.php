@extends('layouts.app')

@section('content')
<div class="container py-5">
    <h1 class="mb-4">Profile Settings</h1>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success">Profile updated successfully.</div>
    @endif

    <form method="POST" action="{{ route('profile.update') }}" class="mb-4">
        @csrf
        @method('PATCH')

        <div class="mb-3">
            <label>Name:</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control">
            @error('name') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control">
            @error('email') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save</button>
    </form>

    <hr>

    <h5 class="mt-4 text-danger">Delete Account</h5>
    <form method="POST" action="{{ route('profile.destroy') }}">
        @csrf
        @method('DELETE')

        <div class="mb-3">
            <label>Confirm Password:</label>
            <input type="password" name="password" class="form-control">
            @error('password') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-danger">Delete My Account</button>
    </form>
</div>
@endsection
