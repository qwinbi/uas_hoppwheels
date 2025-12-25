<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        $profile = $user->adminProfile;
        
        if (!$profile) {
            $profile = $user->adminProfile()->create([
                'full_name' => $user->name,
                'bio' => 'Admin HoppWheels'
            ]);
        }
        
        return view('admin.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string'
        ]);

        $user = auth()->user();
        $profile = $user->adminProfile;

        if ($request->hasFile('photo')) {
            if ($profile->photo) {
                Storage::disk('public')->delete($profile->photo);
            }
            $validated['photo'] = $request->file('photo')->store('admin-profiles', 'public');
        }

        $profile->update($validated);

        return redirect()->route('admin.profile.edit')->with('success', 'Profile berhasil diperbarui.');
    }
}