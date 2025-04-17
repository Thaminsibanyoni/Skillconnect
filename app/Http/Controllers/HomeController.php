<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // Import View

class HomeController extends Controller
{
    /**
     * Show the application homepage.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Fetch any data needed for the homepage later (e.g., featured services)
        return view('home');
    }
}
