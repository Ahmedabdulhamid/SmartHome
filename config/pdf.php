<?php

return [
    /*
    |--------------------------------------------------------------------------
    | إعدادات mPDF الأساسية لدعم اللغة العربية (RTL/UTF-8)
    |--------------------------------------------------------------------------
    */
    'mode'                   => 'utf-8', // <--- تفعيل ترميز UTF-8 لدعم الحروف العربية
    'format'                 => 'A4',
    'default_font_size'      => '14', // يفضل زيادة الحجم للنصوص العربية
    'default_font'           => 'sans-serif', // سنعتمد على الـ CSS لتحديد الخط

    'margin_left'            => 10,
    'margin_right'           => 10,
    'margin_top'             => 10,
    'margin_bottom'          => 10,
    'margin_header'          => 0,
    'margin_footer'          => 0,
    'orientation'            => 'P',

    'title'                  => 'تقرير PDF عربي', // يمكنك تغيير هذا
    'subject'                => '',
    'author'                 => '',

    // ... إعدادات العلامة المائية (Watermark) الأخرى ...

    'watermark'              => '',
    'show_watermark'         => false,
    'show_watermark_image'   => false,
    'watermark_font'         => 'sans-serif',
    'display_mode'           => 'fullpage',
    'watermark_text_alpha'   => 0.1,
    'watermark_image_path'   => '',
    'watermark_image_alpha'  => 0.2,
    'watermark_image_size'   => 'D',
    'watermark_image_position' => 'P',

    /*
    |--------------------------------------------------------------------------
    | إعدادات الخطوط المخصصة (Custom Fonts) لدعم اللغة العربية
    |--------------------------------------------------------------------------
    */

    // تأكد أن لديك مجلد 'resources/fonts' يحتوي على ملفات الخطوط
    'custom_font_dir'        => base_path('resources/fonts/'),

    // تعريف الخطوط: (مثال لإضافة خط "Amiri")
    'custom_font_data'       => [
        'amiri' => [ // هذا هو الاسم الذي ستستخدمه في CSS
            'R' => 'Amiri-Regular.ttf', // اسم ملف الخط العادي
            'B' => 'Amiri-Bold.ttf',    // اسم ملف الخط الغامق
        ],
        // يمكنك إضافة خطوط عربية أخرى هنا...
    ],

    // تفعيل الكشف التلقائي عن اللغة والخطوط (مهم لدعم mPDF)
    'auto_language_detection' => true,

    // إعدادات أخرى
    'temp_dir'               => storage_path('app'),
    'pdfa'                   => false,
    'pdfaauto'               => false,
    'use_active_forms'       => false,
];
