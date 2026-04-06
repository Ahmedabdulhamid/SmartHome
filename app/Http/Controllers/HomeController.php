<?php

namespace App\Http\Controllers;

use App\Services\Frontend\HomeService;

class HomeController extends Controller
{
    public function __construct(
        private readonly HomeService $homeService,
    ) {}

    public function index()
    {
        $currencyCode = session('currency', 'EGP');
        $homeData = $this->homeService->getHomePageData($currencyCode, app()->getLocale());

        return view('home', $homeData);
    }
}
