<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Auth;

class ProfileController
{
    /**
     * Show the profile page
     */
    public function show()
    {
        return view('profile', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the user's profile information
     */
    public function update(ProfileUpdateRequest $request)
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Combine first and last name into the name field
        $name = trim($validated['first_name']);
        if (!empty($validated['last_name'])) {
            $name .= ' ' . trim($validated['last_name']);
        }

        // Update user (email is not editable)
        $user->update([
            'name' => $name,
            'phone' => $validated['phone'],
            'street_address' => $validated['street_address'],
            'city' => $validated['city'],
            'postal_code' => $validated['postal_code'],
            'country' => $validated['country'],
        ]);

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully!');
    }
}
