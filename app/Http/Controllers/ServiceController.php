<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Support\FrontendCache;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function getAllServices(Request $request)
    {
        $services = FrontendCache::remember('all_services', [
            'locale' => app()->getLocale(),
            'page' => (int) $request->integer('page', 1),
        ], 900, function () {
            return Service::query()
                ->where('is_active', true)
                ->with('category', 'baseCurrency', 'features')
                ->paginate(12);
        });

        return view('services.index', compact('services'));
    }

    public function getServiceBySlug($slug)
    {
        $service = FrontendCache::remember('service_detail', [
            'slug' => $slug,
            'locale' => app()->getLocale(),
        ], 900, function () use ($slug) {
            return Service::query()
                ->where('slug', $slug)
                ->where('is_active', true)
                ->with('category', 'baseCurrency', 'features')
                ->firstOrFail();
        });

        return view('services.show', compact('service'));
    }
}
