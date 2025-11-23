<!DOCTYPE html>

{{-- 💡 تحديد اللغة واتجاه النص والمحاذاة --}}
@php
    $lang = app()->getLocale();
    $dir = ($lang == 'ar') ? 'rtl' : 'ltr';
    $align = ($dir == 'rtl') ? 'right' : 'left';
@endphp

<html lang="{{ $lang }}" dir="{{ $dir }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- استخدام الترجمة للعنوان --}}
    <title>{{ __('web.contact_reply_subject') }}</title>
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            /* استخدام المتغيرات الديناميكية */
            direction: {{ $dir }};
            text-align: {{ $align }};
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            direction: {{ $dir }}; /* تحديد الاتجاه على مستوى الحاوية */
        }

        /* تنسيق رأس اللوجو */
        .header-logo {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header-logo img {
            max-width: 150px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        h1 {
            color: #1e40af;
            border-bottom: 2px solid #eff6ff;
            padding-bottom: 10px;
            margin-top: 0;
            font-size: 24px;
            text-align: {{ $align }};
        }

        .content-body {
            padding: 15px 0;
            text-align: {{ $align }};
        }

        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #eee;
            font-size: 12px;
            color: #666;
            text-align: {{ $align }};
        }
    </style>
</head>

<body>
    <div class="container">

        {{-- ⭐️ عرض اللوجو باستخدام الـ URL المُمرر من الـ Mailable --}}
        @if (isset($logoUrl) && $logoUrl)
            <div class="header-logo">
                <img src="{{ $logoUrl }}" alt="{{ __('web.company_name') }}" title="{{ __('web.company_name') }}">
            </div>
        @endif

        {{-- الترجمة: السلام عليكم --}}
        <h1>{{ __('web.greeting') }}</h1>

        <div class="content-body">
            {{-- عرض رسالة الرد. نستخدم {!! !!} لعرض النص مع الحفاظ على فواصل الأسطر --}}
            <p style="text-align: {{ $align }}; white-space: pre-wrap; word-wrap: break-word;">{!! nl2br(e($replyMessage)) !!}</p>
        </div>

        <p style="text-align: {{ $align }};">
            {{-- الترجمة: شكراً لتواصلك معنا --}}
            {{ __('web.thanks_for_contact') }}
        </p>

        <div class="footer">
            <p>
                {{-- الترجمة: تحياتنا، فريق الدعم --}}
                {{ __('web.regards') }}،<br>
                {{ __('web.support_team') }}
            </p>
        </div>
    </div>

</body>

</html>
