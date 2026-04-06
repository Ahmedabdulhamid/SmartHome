<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\BlogRepository;
use App\Support\FrontendCache;

class BlogService
{
    public function __construct(
        private readonly BlogRepository $blogs,
    ) {}

    public function getAllBlogs(string $locale, int $page)
    {
        return FrontendCache::remember('all_blogs', [
            'locale' => $locale,
            'page' => $page,
        ], 900, fn () => $this->blogs->paginateAll());
    }

    public function getBlogBySlug(string $slug, string $locale)
    {
        return FrontendCache::remember('blog_detail', [
            'slug' => $slug,
            'locale' => $locale,
        ], 300, fn () => $this->blogs->findBySlug($slug));
    }
}
