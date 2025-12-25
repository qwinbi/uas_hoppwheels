<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function index()
    {
        $about = About::first();
        
        if (!$about) {
            $about = About::create([
                'description' => 'Website rental mobil Honda terpercaya.',
                'biodata' => json_encode([
                    'name' => 'Syarifatul Azkiya Alganjari',
                    'nim' => '241011701321',
                    'matkul' => 'Rekayasa Web'
                ])
            ]);
        }
        
        return response()->json([
            'success' => true,
            'data' => $about
        ]);
    }
}