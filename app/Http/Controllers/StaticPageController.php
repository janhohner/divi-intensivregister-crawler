<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

class StaticPageController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function impressum()
    {
        return view('impressum');
    }
}
