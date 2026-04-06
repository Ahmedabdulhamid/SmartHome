<?php

namespace App\Http\Controllers;

use App\Services\Frontend\BlogService;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(
        private readonly BlogService $blogs,
    ) {}

    public function getAllBlogs(Request $request)
    {
        $blogs = $this->blogs->getAllBlogs(
            app()->getLocale(),
            (int) $request->integer('page', 1),
        );

        return view('blogs.index', compact('blogs'));
    }

    public function getBlogBySlug($slug)
    {
        $blog = $this->blogs->getBlogBySlug($slug, app()->getLocale());

        $blog->incrementViews();

        return view('blogs.show', compact('blog'));
    }
}
