<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\FrontendComposer;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
                /*
        |--------------------------------------------------------------------------
        | FRONTEND VIEW COMPOSER
        |--------------------------------------------------------------------------
        |
        | Makes $latestVideo available to every view under
        | resources/views/frontend/* and to the master layout.
        |
        */

        View::composer(
            ['frontend.*', 'frontend.master'],
            FrontendComposer::class
        );
    }
}
