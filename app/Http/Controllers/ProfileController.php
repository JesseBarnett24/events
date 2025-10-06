<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    // Display the profile edit form for the authenticated user
    // @param Request $request
    // @return \Illuminate\View\View
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    // Update the user's profile details such as name and email
    // @param Request $request
    // @return \Illuminate\Http\RedirectResponse
    public function update(Request $request)
    {
        $user = $request->user();

        // Validate updated profile information
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        // Detect if the email has changed to reset verification if needed
        $emailChanged = $validated['email'] !== $user->email;

        $user->fill($validated);

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    // Permanently delete the authenticated user's account after verifying password
    // @param Request $request
    // @return \Illuminate\Http\RedirectResponse
    public function destroy(Request $request)
    {
        // Confirm the user's current password before deletion
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // Log out user and delete their record
        auth()->logout();
        $user->delete();

        // Invalidate session and regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
