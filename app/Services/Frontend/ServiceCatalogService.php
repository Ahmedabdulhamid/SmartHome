<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\ServiceRepository;
use App\Support\FrontendCache;

class ServiceCatalogService
{
    public function __construct(
        private readonly ServiceRepository $services,
    ) {}

    public function getAllServices(string $locale, int $page)
    {
        return FrontendCache::remember('all_services', [
            'locale' => $locale,
            'page' => $page,
        ], 900, fn () => $this->services->paginateActive());
    }

    public function getServiceBySlug(string $slug, string $locale)
    {
        return FrontendCache::remember('service_detail', [
            'slug' => $slug,
            'locale' => $locale,
        ], 900, fn () => $this->services->findActiveBySlug($slug));
    }
}
