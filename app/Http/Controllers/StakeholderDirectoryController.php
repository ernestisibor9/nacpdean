<?php

namespace App\Http\Controllers;

use App\Models\StakeholderDirectory;
use Illuminate\Http\Request;

class StakeholderDirectoryController extends Controller
{
    //
        public function index()
    {
        $files = StakeholderDirectory
        ::where('is_active', true)
            ->orderByDesc('id')
            ->get();

        return view('frontend.stakeholders-data', compact('files'));
    }

    public function view(StakeholderDirectory $stakeholderDirectory)
    {
        if (!$stakeholderDirectory->is_active) {
            abort(404);
        }

        $path = public_path('uploads/stakeholder-directories/' . $stakeholderDirectory->file_path);
        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path);
    }
}
