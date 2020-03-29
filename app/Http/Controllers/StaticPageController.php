<?php

namespace App\Http\Controllers;

class StaticPageController extends Controller
{
    public function impressum()
    {
        return view('impressum');
    }
}
