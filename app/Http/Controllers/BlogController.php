<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function getAllBlogs()
    {
        $blogs = \App\Models\Blog::latest()->paginate(12);
        return view('blogs.index', compact('blogs'));
    }
    public function getBlogBySlug($slug)
    {
        $blog = \App\Models\Blog::where('slug', $slug)->firstOrFail();
        $blog->incrementViews();

        return view('blogs.show', compact('blog'));
    }
}
