<?php

namespace App\View\Composers;

use App\Models\HomeVideo;
use Illuminate\View\View;

class FrontendComposer
{
    /**
     * Share the latest active video with every frontend view.
     *
     * The "featured video" modal in frontend.master uses this
     * variable, so it must be available on every page that
     * extends the master layout.
     */
    public function compose(View $view): void
    {
        $view->with(
            'latestVideo',
            HomeVideo::where('is_active', true)
                ->orderByDesc('id')
                ->first()
        );
    }
}
