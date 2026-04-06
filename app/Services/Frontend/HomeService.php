<?php

namespace App\Services\Frontend;

use App\Repositories\Frontend\HomeRepository;
use App\Support\FrontendCache;

class HomeService
{
    public function __construct(
        private readonly HomeRepository $homeRepository,
    ) {}

    public function getHomePageData(string $currencyCode, string $locale): array
    {
        return FrontendCache::remember('home_page', [
            'locale' => $locale,
            'currency' => $currencyCode,
        ], 900, function () use ($currencyCode) {
            $currency = $this->homeRepository->findCurrencyByCode($currencyCode);

            return [
                'categories' => $this->homeRepository->getFeaturedCategories(),
                'brands' => $this->homeRepository->getFeaturedBrands(),
                'products' => $this->homeRepository->getHomeProductsByCurrency($currency->id),
                'downloads' => $this->homeRepository->getDownloads(),
                'services' => $this->homeRepository->getFeaturedServices(),
                'blogs' => $this->homeRepository->getLatestBlogs(),
            ];
        });
    }
}
