<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StakeholderDirectory;
use Illuminate\Http\Request;
use App\Models\StakeholderFile;
use Illuminate\Support\Str;

class StakeholderDirectoryController extends Controller
{
    //
        public function index()
    {
        $files = StakeholderDirectory::orderByDesc('id')->paginate(20);
        return view('admin.stakeholder-files.index', compact('files'));
    }

    public function create()
    {
        return view('admin.stakeholder-files.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'file'  => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:20480'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | UPLOAD DIRECTORY — public/uploads/stakeholder-files
        |--------------------------------------------------------------------------
        */

        $dir = public_path('uploads/stakeholder-directories');

        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $request->file('file');
        $ext  = strtolower($file->getClientOriginalExtension());

        $name = 'shf_' . time() . '_' . Str::random(10) . '.' . $ext;
        $file->move($dir, $name);

        StakeholderDirectory::create([
            'title'     => $request->title,
            'file_path' => $name,
            'file_type' => $ext === 'pdf' ? 'pdf' : 'image',
        ]);

        return redirect()
            ->route('admin.stakeholder-files.index')
            ->with('success', 'File uploaded successfully.');
    }

    public function destroy(StakeholderDirectory  $stakeholderFile)
    {
        $path = public_path('uploads/stakeholder-directories/' . $stakeholderFile->file_path);

        if (file_exists($path)) {
            @unlink($path);
        }

        $stakeholderFile->delete();

        return back()->with('success', 'File deleted.');
    }

    public function toggle(StakeholderDirectory $stakeholderFile)
    {
        $stakeholderFile->update([
            'is_active' => !$stakeholderFile->is_active,
        ]);

        return back()->with('success', 'Visibility updated.');
    }
}
