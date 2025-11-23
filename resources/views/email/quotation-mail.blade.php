<!DOCTYPE html>

{{-- 💡 Determine the language and set direction --}}
@php
use App\Models\Setting;
    $lang = app()->getLocale();
    $dir = ($lang == 'ar') ? 'rtl' : 'ltr';
    $setting=Setting::first();
@endphp

<html lang="{{ $lang }}" dir="{{ $dir }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Use translation for the title --}}
    <title>{{ __('web.quotation_title') }}</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            /* Use the dynamic direction */
            direction: {{ $dir }};
            text-align: {{ ($dir == 'rtl') ? 'right' : 'left' }};
            background-color: #f4f4f4;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #1e40af;
            border-bottom: 2px solid #eff6ff;
            padding-bottom: 10px;
        }

        p {
            line-height: 1.6;
            color: #333;
        }

        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3b82f6;
            /* Tailwind blue-500 */
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 15px;
            text-align: center;
        }

        .warning {
            color: #9d174d;
            /* Tailwind rose-700 */
            font-size: 13px;
            margin-top: 10px;
            border: 1px solid #fbcfe8;
            background-color: #fff1f2;
            padding: 10px;
            border-radius: 4px;
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>

<body>
    <div class="container">
         @if ($setting && $setting->site_logo)
            <div class="header-logo">
                <img src="{{ url('public/storage/'.$setting->site_logo) }}" alt="{{ __('web.company_name') }}" title="{{ __('web.company_name') }}">
            </div>
        @endif
        {{-- Use translation for the heading --}}
        <h1>{{ __('web.hello') }}</h1>

        {{-- Use translation with a placeholder for the quotation ID --}}
        <p>{{ __('web.quotation_intro', ['id' => $quotation->id]) }}</p>

        <a href="{{ $secureUrl }}" class="button" target="_blank">{{ __('web.download_pdf') }}</a>

        <p class="warning">
            {{-- Use translation for the warning text, including the bold note --}}
            <strong>{{ __('web.important_note') }}</strong> {{ __('web.warning_message') }}
        </p>

        <p>
            {{-- Use translation for the contact message --}}
            {{ __('web.contact_us') }}
        </p>

        <div class="footer">
            {{-- Use translation for the footer text --}}
            <p>{{ __('web.regards') }}</p>
        </div>
    </div>

</body>

</html>
