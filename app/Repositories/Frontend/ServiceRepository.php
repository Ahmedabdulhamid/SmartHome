<?php

namespace App\Repositories\Frontend;

use App\Models\Service;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ServiceRepository
{
    public function paginateActive(int $perPage = 12): LengthAwarePaginator
    {
        return Service::query()
            ->where('is_active', true)
            ->with('category', 'baseCurrency', 'features')
            ->paginate($perPage);
    }

    public function findActiveBySlug(string $slug): Service
    {
        return Service::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with('category', 'baseCurrency', 'features')
            ->firstOrFail();
    }
}
