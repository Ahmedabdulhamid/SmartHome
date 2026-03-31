<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Support\FrontendCache;

class PageController extends Controller
{
    public function goToPage($slug)
    {
        $page = FrontendCache::remember('static_page', [
            'slug' => $slug,
            'locale' => app()->getLocale(),
        ], 1800, function () use ($slug) {
            return Page::query()->whereSlug($slug)->firstOrFail();
        });

        return view('pages.pages', ['page' => $page]);
    }
}
