<?php

namespace App\Repositories\Frontend;

use App\Models\Blog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BlogRepository
{
    public function paginateAll(int $perPage = 12): LengthAwarePaginator
    {
        return Blog::query()
            ->with(['category', 'author'])
            ->latest()
            ->paginate($perPage);
    }

    public function findBySlug(string $slug): Blog
    {
        return Blog::query()
            ->with(['category', 'author'])
            ->where('slug', $slug)
            ->firstOrFail();
    }
}
