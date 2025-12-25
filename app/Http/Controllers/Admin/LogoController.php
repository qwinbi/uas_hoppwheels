<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Logo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    public function index()
    {
        $logos = Logo::all();
        return view('admin.logos.index', compact('logos'));
    }

    public function create()
    {
        return view('admin.logos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alt_text' => 'nullable|string|max:255'
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('logos', 'public');
        }

        Logo::create($validated);

        return redirect()->route('admin.logos.index')->with('success', 'Logo berhasil ditambahkan.');
    }

    public function edit(Logo $logo)
    {
        return view('admin.logos.edit', compact('logo'));
    }

    public function update(Request $request, Logo $logo)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'alt_text' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        if ($request->hasFile('image')) {
            if ($logo->image) {
                Storage::disk('public')->delete($logo->image);
            }
            $validated['image'] = $request->file('image')->store('logos', 'public');
        }

        $logo->update($validated);

        return redirect()->route('admin.logos.index')->with('success', 'Logo berhasil diperbarui.');
    }

    public function destroy(Logo $logo)
    {
        if ($logo->image) {
            Storage::disk('public')->delete($logo->image);
        }
        
        $logo->delete();

        return redirect()->route('admin.logos.index')->with('success', 'Logo berhasil dihapus.');
    }

    public function setActive(Logo $logo)
    {
        Logo::where('is_active', true)->update(['is_active' => false]);
        $logo->update(['is_active' => true]);

        return redirect()->route('admin.logos.index')->with('success', 'Logo aktif berhasil diubah.');
    }
}