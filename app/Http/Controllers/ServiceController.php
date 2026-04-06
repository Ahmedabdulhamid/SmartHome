<?php

namespace App\Http\Controllers;

use App\Services\Frontend\ServiceCatalogService;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct(
        private readonly ServiceCatalogService $services,
    ) {}

    public function getAllServices(Request $request)
    {
        $services = $this->services->getAllServices(
            app()->getLocale(),
            (int) $request->integer('page', 1),
        );

        return view('services.index', compact('services'));
    }

    public function getServiceBySlug($slug)
    {
        $service = $this->services->getServiceBySlug($slug, app()->getLocale());

        return view('services.show', compact('service'));
    }
}
