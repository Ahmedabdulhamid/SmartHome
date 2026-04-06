<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\PageRepository;
use App\Support\FrontendCache;

class PageService
{
    public function __construct(
        private readonly PageRepository $pages,
    ) {}

    public function getBySlug(string $slug, string $locale)
    {
        return FrontendCache::remember('static_page', [
            'slug' => $slug,
            'locale' => $locale,
        ], 1800, fn () => $this->pages->findBySlug($slug));
    }
}
