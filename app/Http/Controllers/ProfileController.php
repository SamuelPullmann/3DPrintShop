<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'street_address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
        ]);

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
