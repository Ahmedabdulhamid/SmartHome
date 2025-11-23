<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function getAllServices()
    {
        $services = \App\Models\Service::where('is_active', true)
            ->with('category', 'baseCurrency', 'features')
            ->paginate(12);

        return view('services.index', compact('services'));
    }
    public function getServiceBySlug($slug)
    {

        $service = \App\Models\Service::where('slug', $slug)
            ->where('is_active', true)
            ->with('category', 'baseCurrency', 'features')
            ->firstOrFail();


        return view('services.show', compact('service'));
    }

}
