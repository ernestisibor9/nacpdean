<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;

class GalleryController extends Controller
{
    public function index()
    {
        $photos = GalleryPhoto::where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(24);   // 24 per page

        return view('frontend.gallery', compact('photos'));
    }
}
