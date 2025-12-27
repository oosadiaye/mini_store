<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class LandingController extends Controller
{
    /**
     * Show the application landing page.
     */
    public function index(): View
    {
        $branding = \App\Models\GlobalSetting::where('group', 'branding')->pluck('value', 'key');
        return view('welcome', compact('branding'));
    }
}
