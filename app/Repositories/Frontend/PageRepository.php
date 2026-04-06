<?php

namespace App\Repositories\Frontend;

use App\Models\Page;

class PageRepository
{
    public function findBySlug(string $slug): Page
    {
        return Page::query()->whereSlug($slug)->firstOrFail();
    }
}
