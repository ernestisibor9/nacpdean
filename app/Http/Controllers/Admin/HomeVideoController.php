<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeVideoController extends Controller
{
    public function index()
    {
        $videos = HomeVideo::orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.home-videos.index', compact('videos'));
    }

    public function create()
    {
        return view('admin.home-videos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'source_type' => ['required', 'in:youtube,file'],

            // Conditional
            'youtube_url' => ['nullable', 'required_if:source_type,youtube', 'string', 'max:500'],
            'video_file'  => ['nullable', 'required_if:source_type,file', 'file', 'mimes:mp4,webm,mov,ogg', 'max:102400'], // 100 MB
            'thumbnail'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $sourceValue = null;

        if ($request->source_type === 'youtube') {

            $sourceValue = trim($request->youtube_url);

        } else {

            /*
            |--------------------------------------------------------------------------
            | UPLOAD LOCAL VIDEO
            |--------------------------------------------------------------------------
            */

            $dir = public_path('uploads/videos');

            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }

            $file = $request->file('video_file');
            $ext  = strtolower($file->getClientOriginalExtension());

            $sourceValue = 'vid_' . time() . '_' . Str::random(10) . '.' . $ext;

            $file->move($dir, $sourceValue);

            /*
            |--------------------------------------------------------------------------
            | OPTIONAL THUMBNAIL
            |--------------------------------------------------------------------------
            */

            $thumbName = null;

            if ($request->hasFile('thumbnail')) {

                $thumbDir = $dir . DIRECTORY_SEPARATOR . 'thumbs';

                if (!file_exists($thumbDir)) {
                    mkdir($thumbDir, 0755, true);
                }

                $thumb = $request->file('thumbnail');
                $thumbExt = strtolower($thumb->getClientOriginalExtension());

                $thumbName = 'thm_' . time() . '_' . Str::random(10) . '.' . $thumbExt;

                $thumb->move($thumbDir, $thumbName);
            }

            HomeVideo::create([
                'title'        => $request->title,
                'description'  => $request->description,
                'source_type'  => 'file',
                'source_value' => $sourceValue,
                'thumbnail'    => $thumbName,
                'is_active'    => true,
            ]);

            return redirect()
                ->route('admin.home-videos.index')
                ->with('success', 'Video uploaded successfully.');
        }

        /*
        |--------------------------------------------------------------------------
        | YOUTUBE PATH
        |--------------------------------------------------------------------------
        */

        HomeVideo::create([
            'title'        => $request->title,
            'description'  => $request->description,
            'source_type'  => 'youtube',
            'source_value' => $sourceValue,
            'is_active'    => true,
        ]);

        return redirect()
            ->route('admin.home-videos.index')
            ->with('success', 'YouTube video added successfully.');
    }

    public function update(Request $request, HomeVideo $homeVideo)
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $homeVideo->update([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        return back()->with('success', 'Video updated.');
    }

    public function destroy(HomeVideo $homeVideo)
    {
        /*
        |--------------------------------------------------------------------------
        | DELETE LOCAL FILES
        |--------------------------------------------------------------------------
        */

        if ($homeVideo->source_type === 'file') {

            $filePath = public_path('uploads/videos/' . $homeVideo->source_value);

            if (file_exists($filePath)) {
                @unlink($filePath);
            }

            if ($homeVideo->thumbnail) {
                $thumbPath = public_path('uploads/videos/thumbs/' . $homeVideo->thumbnail);

                if (file_exists($thumbPath)) {
                    @unlink($thumbPath);
                }
            }
        }

        $homeVideo->delete();

        return back()->with('success', 'Video deleted.');
    }

    public function toggle(HomeVideo $homeVideo)
    {
        $homeVideo->update([
            'is_active' => !$homeVideo->is_active,
        ]);

        return back()->with('success', 'Visibility updated.');
    }
}
