<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $fillable = [
        'description',
        'banner_image',
        'biodata'
    ];

    protected $casts = [
        'biodata' => 'array'
    ];

    public function getBannerImageUrlAttribute()
    {
        return $this->banner_image ? asset('storage/' . $this->banner_image) : asset('images/default-banner.jpg');
    }
}