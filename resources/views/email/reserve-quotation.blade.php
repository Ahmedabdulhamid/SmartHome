<!DOCTYPE html>

{{-- 💡 تحديد اللغة وتعيين الاتجاه (Direction) --}}
@php
use App\Models\Setting;
    $lang = app()->getLocale();
    $dir = ($lang == 'ar') ? 'rtl' : 'ltr';
    $align = ($lang == 'ar') ? 'right' : 'left';
       $setting=Setting::first();
@endphp

<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- استخدام الترجمة للعنوان --}}
    <title>{{ __('web.reservation_subject') }}</title>
    <style>
        /* التنسيقات الأساسية لضمان القراءة على جميع العملاء (Gmail, Outlook, etc.) */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333333;
            line-height: 1.6;
            /* استخدام المتغير لتحديد المحاذاة حسب اللغة */
            text-align: {{ $align }};
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* استخدام المتغير لتحديد الاتجاه */
            direction: {{ $dir }};
        }
        h1 {
            color: #007bff !important; /* لون أزرق جذاب */
            border-bottom: 2px solid #eeeeee !important;
            padding-bottom: 10px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 15px 0;
            background-color: #28a745 !important; /* لون أخضر للنجاح */
            color: #ffffff !important;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eeeeee;
            font-size: 0.9em;
            color: #777777;
        }
        /* لتنسيق النصوص القوية داخل النصوص العادية RTL/LTR */
        strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
         @if ($setting && $setting->site_logo)
            <div class="header-logo">
                <img src="{{ url('storage/'.$setting->site_logo) }}" alt="{{ __('email.company_name') }}" title="{{ __('email.company_name') }}">
            </div>
        @endif
        <h1>{{ __('web.reservation_title') }}</h1>

        <p>{{ __('web.dear_customer') }}</p>

        {{-- استخدام الترجمة مع تمرير الـ ID كـ Placeholder --}}
        <p>{{ __('web.reservation_success', ['id' => $quotation->id]) }}</p>

        <p>
            <strong>{{ __('web.reservation_details') }}</strong><br>
            {{ __('web.quotation_number') }} <strong>{{ $quotation->id }}</strong><br>
            {{-- استخدام دالة now() الخاصة بـ Laravel مع تنسيق الوقت --}}
            {{ __('web.reservation_date') }} <strong>{{ now()->format('Y-m-d H:i') }}</strong>
        </p>


        <div class="footer">
            {{-- استخدام الترجمة لملاحظة الرد الآلي --}}
            <p>{{ __('web.auto_reply_note') }}</p>
        </div>
    </div>
</body>
</html>
