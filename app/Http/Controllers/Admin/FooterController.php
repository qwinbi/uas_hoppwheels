<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Footer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FooterController extends Controller
{
    public function edit()
    {
        $footer = Footer::first();
        if (!$footer) {
            $footer = Footer::create([
                'text' => 'Text footer default',
                'copyright' => '© ' . date('Y') . ' HoppWheels. All rights reserved.',
                'links' => json_encode([])
            ]);
        }
        
        return view('admin.footer.edit', compact('footer'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'copyright' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'links' => 'nullable|string'
        ]);

        $footer = Footer::first();
        
        $links = [];
        if ($validated['links']) {
            $linksArray = explode(',', $validated['links']);
            foreach ($linksArray as $link) {
                $parts = explode('|', trim($link));
                if (count($parts) >= 2) {
                    $links[] = [
                        'name' => trim($parts[0]),
                        'url' => trim($parts[1])
                    ];
                }
            }
        }

        if ($request->hasFile('logo')) {
            if ($footer->logo) {
                Storage::disk('public')->delete($footer->logo);
            }
            $validated['logo'] = $request->file('logo')->store('footer', 'public');
        }

        $footer->update([
            'text' => $validated['text'],
            'copyright' => $validated['copyright'],
            'logo' => $validated['logo'] ?? $footer->logo,
            'links' => json_encode($links)
        ]);

        return redirect()->route('admin.footer.edit')->with('success', 'Footer berhasil diperbarui.');
    }
}