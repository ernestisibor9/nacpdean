<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    //
    public function Index()
    {
        return view('frontend.index');
    }

    public function About()
    {
        return view('frontend.about');
    }

    public function Bot()
    {
        return view('frontend.bot');
    }

    public function History()
    {
        return view('frontend.history');
    }

    public function Association()
    {
        return view('frontend.association');
    }

    public function Partnership()
    {
        return view('frontend.partnership');
    }

    public function NationalExecutive()
    {
        return view('frontend.national-executive');
    }

    public function StateExecutive()
    {
        return view('frontend.state-executive');
    }
}
