<?php

namespace App\Http\Controllers;

use App\Models\Page; // Import the Page model
use Illuminate\Http\Request;
use Illuminate\View\View; // Import View

class PageController extends Controller
{
    /**
     * Display the specified page.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function show(string $slug): View
    {
        // Find the page by slug, but only if it's published
        $page = Page::where('slug', $slug)->where('status', 'published')->firstOrFail();

        // Pass the page data to a view
        return view('pages.show', compact('page'));
    }
}
