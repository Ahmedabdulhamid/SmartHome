@php
use App\Models\Setting;
    $settings = Setting::find(1);

@endphp

<img src="{{ asset('storage/' . $settings->site_logo) }}" alt="Logo" class="w-32 h-auto">


