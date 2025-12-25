<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function edit()
    {
        $about = About::first();
        if (!$about) {
            $about = About::create([
                'description' => 'Deskripsi default',
                'biodata' => json_encode([
                    'name' => 'Syarifatul Azkiya Alganjari',
                    'nim' => '241011701321',
                    'matkul' => 'Rekayasa Web'
                ])
            ]);
        }
        
        return view('admin.about.edit', compact('about'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'description' => 'required|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'name' => 'required|string|max:255',
            'nim' => 'required|string|max:50',
            'matkul' => 'required|string|max:255'
        ]);

        $about = About::first();
        
        $biodata = [
            'name' => $validated['name'],
            'nim' => $validated['nim'],
            'matkul' => $validated['matkul']
        ];

        if ($request->hasFile('banner_image')) {
            if ($about->banner_image) {
                Storage::disk('public')->delete($about->banner_image);
            }
            $validated['banner_image'] = $request->file('banner_image')->store('about', 'public');
        }

        $about->update([
            'description' => $validated['description'],
            'banner_image' => $validated['banner_image'] ?? $about->banner_image,
            'biodata' => json_encode($biodata)
        ]);

        return redirect()->route('admin.about.edit')->with('success', 'Halaman About berhasil diperbarui.');
    }
}