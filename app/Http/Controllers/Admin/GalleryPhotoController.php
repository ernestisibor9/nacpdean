<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryPhotoController extends Controller
{
    public function index()
    {
        $photos = GalleryPhoto::orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(24);

        return view('admin.gallery.index', compact('photos'));
    }

    public function create()
    {
        return view('admin.gallery.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => ['nullable', 'string', 'max:255'],
            'caption'  => ['nullable', 'string', 'max:500'],
            'photos'   => ['required', 'array', 'min:1'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        $dir = public_path('uploads/gallery');

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $inserted = 0;

        foreach ($request->file('photos') as $photo) {

            $ext  = strtolower($photo->getClientOriginalExtension());
            $name = 'gal_' . time() . '_' . Str::random(10) . '.' . $ext;

            $photo->move($dir, $name);

            GalleryPhoto::create([
                'title'     => $request->title,
                'caption'   => $request->caption,
                'file_path' => $name,
            ]);

            $inserted++;
        }

        return redirect()
            ->route('admin.gallery.index')
            ->with('success', $inserted . ' photo(s) uploaded successfully.');
    }

    public function update(Request $request, GalleryPhoto $galleryPhoto)
    {
        $request->validate([
            'title'   => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        $galleryPhoto->update([
            'title'   => $request->title,
            'caption' => $request->caption,
        ]);

        return back()->with('success', 'Photo updated.');
    }

    public function destroy(GalleryPhoto $galleryPhoto)
    {
        $path = public_path('uploads/gallery/' . $galleryPhoto->file_path);

        if (file_exists($path)) {
            @unlink($path);
        }

        $galleryPhoto->delete();

        return back()->with('success', 'Photo deleted.');
    }

    public function toggle(GalleryPhoto $galleryPhoto)
    {
        $galleryPhoto->update([
            'is_active' => !$galleryPhoto->is_active,
        ]);

        return back()->with('success', 'Visibility updated.');
    }
}
