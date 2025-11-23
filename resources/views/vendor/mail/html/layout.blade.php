@php
    use App\Models\Setting;
    $setting = Setting::first();
@endphp
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="rtl">

<head>
    <title>{{ __('web.ry_company') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <style>
        @media only screen and (max-width: 600px) {
            .inner-body {
                width: 100% !important;
            }

            .footer {
                width: 100% !important;
            }
        }

        @media only screen and (max-width: 500px) {
            .button {
                width: 100% !important;
            }
        }
    </style>
    {!! $head ?? '' !!}
</head>

<body dir="rtl">

    <table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center">
                <table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">

                    <tr>
                        <td class="header">
                            <a href="{{ url('/') }}" style="display: inline-block;">
                                <!--
                                    لتصحيح مشكلة ظهور اللوجو:
                                    1. يجب التأكد أن Storage::url() ينتج رابطًا كاملاً (Absolute URL).
                                    2. إذا كان الملف في public، استخدم asset().
                                    للاختبار المبدئي، سنحافظ على الكود كما هو ولكن تأكد من تفعيل Storage Link!
                                -->
                                <img src="http://127.0.0.1:8000/storage/{{ $setting->site_logo }}" class="logo"
                                    alt="{{ __('web.ry_company') }}"
                                    onerror="this.onerror=null; this.src='{{ asset('img/fallback-logo.png') }}'">
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td class="body" width="100%" cellpadding="0" cellspacing="0"
                            style="border: hidden !important;">
                            <table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0"
                                role="presentation">
                                <tr>
                                    <td class="content-cell">
                                        {!! Illuminate\Mail\Markdown::parse($slot) !!}

                                        {!! $subcopy ?? '' !!}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {!! $footer ?? '' !!}
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
