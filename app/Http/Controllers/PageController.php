<?php

namespace App\Http\Controllers;

use App\Services\Frontend\PageService;

class PageController extends Controller
{
    public function __construct(
        private readonly PageService $pages,
    ) {}

    public function goToPage($slug)
    {
        $page = $this->pages->getBySlug($slug, app()->getLocale());

        return view('pages.pages', ['page' => $page]);
    }
}
