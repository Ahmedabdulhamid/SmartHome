<style>
    /*
    يجب وضع هذا الكود في ملف CSS الذي يتم تجميعه بواسطة Vite (مثل app.css)
    لضمان أن التنسيقات تصل إلى الواجهة الأمامية.
    */

    .footer-content-wrapper table {
        /* جعل الجدول يأخذ عرضاً معقولاً */
        width: 100%;
        max-width: 600px; /* أو أي عرض مناسب */
        margin-left: auto;
        margin-right: auto;
        border-collapse: collapse; /* دمج حدود الجدول */
        text-align: right; /* محاذاة النص داخل الجدول ليتناسب مع العربية */
    }

    .footer-content-wrapper table th,
    .footer-content-wrapper table td {
        border: 1px solid #d1d5db; /* إضافة حدود خفيفة */
        padding: 8px 12px;
        vertical-align: top;
    }

    .footer-content-wrapper table th {
        background-color: #e5e7eb; /* خلفية خفيفة للرؤوس */
        font-weight: 600;
    }

    /* تنسيق الفقرات لضمان المحاذاة */
    .footer-content-wrapper p {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        text-align: inherit; /* لضمان المحاذاة حسب الاتجاه */
    }

    /* تعديل عرض القوائم */
    .footer-content-wrapper ul,
    .footer-content-wrapper ol {
        padding-right: 20px; /* لضمان ظهور الرموز أو الأرقام */
        margin: 10px auto;
        text-align: right;
        display: inline-block; /* لمركزة القائمة إذا لزم الأمر */
    }

    /* لضمان تطبيق الألوان المضافة من المحرر */
    .footer-content-wrapper span[style*="background-color"],
    .footer-content-wrapper div[style*="background-color"] {
        padding: 2px 5px;
        border-radius: 3px;
        line-height: 1.5;
    }
</style>
<div class="max-w-3xl mx-auto bg-white shadow-2xl rounded-2xl overflow-hidden border border-gray-300"
     style="font-family: {{ $template->default_font }};">

    {{-- Header --}}
    <div class="px-8 py-6 border-b flex justify-between items-center"
         style="background-color: {{ $template->color_scheme ?? '#f3f4f6' }}25; color: {{ $template->color_scheme ?? '#000' }}">

        {{-- Logo --}}
        @if($template->logo)
            <div class="flex-shrink-0">
                <img src="{{ Storage::url($template->logo) }}" alt="Logo" class="h-14 object-contain">
            </div>
        @endif

        {{-- Header HTML --}}
        <div class="flex-1 text-center font-bold text-xl tracking-wide">
            {!! $template->getTranslation('header_html', app()->getLocale()) !!}
        </div>
    </div>

    {{-- Body Content --}}
    <div class="p-8">
        {!! $template->getTranslation('body_html', app()->getLocale()) !!}
    </div>

    {{-- Footer (الفوتر) --}}
    {{-- تم لف محتوى الفوتر بـ wrapper لتطبيق تنسيقات الجداول والقوائم --}}
    <div class="px-8 py-4 border-t text-center text-sm"
         style="background-color: {{ $template->primary_color ?? '#f9f9f9' }}15; color: {{ $template->color_scheme ?? '#4b5563' }}">

        {{-- إضافة wrapper لتطبيق التنسيق CSS الخاص بـ .footer-content-wrapper --}}
        <div class="footer-content-wrapper mx-auto max-w-full">
            {!! $template->getTranslation('footer_html', app()->getLocale()) !!}
        </div>
    </div>
</div>
{{-- تم حذف عرض الفوتر المكرر من هنا --}}
