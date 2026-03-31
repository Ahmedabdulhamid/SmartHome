<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Support\FrontendCache;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function getAllBlogs(Request $request)
    {
        $blogs = FrontendCache::remember('all_blogs', [
            'locale' => app()->getLocale(),
            'page' => (int) $request->integer('page', 1),
        ], 900, function () {
            return Blog::query()
                ->with(['category', 'author'])
                ->latest()
                ->paginate(12);
        });

        return view('blogs.index', compact('blogs'));
    }

    public function getBlogBySlug($slug)
    {
        $blog = FrontendCache::remember('blog_detail', [
            'slug' => $slug,
            'locale' => app()->getLocale(),
        ], 300, function () use ($slug) {
            return Blog::query()
                ->with(['category', 'author'])
                ->where('slug', $slug)
                ->firstOrFail();
        });

        $blog->incrementViews();

        return view('blogs.show', compact('blog'));
    }
}
