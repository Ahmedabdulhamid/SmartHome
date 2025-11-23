import preset from './vendor/filament/filament/tailwind.config.js'

export default {
    // 💡 الخطوة 1: تضمين إعدادات Filament المسبقة
    presets: [preset],

    // 💡 الخطوة 2: توسيع مسارات المحتوى لتشمل ملفات Filament
    content: [
        // مسارات ملفاتك الحالية
        './app/Filament/**/*.php',
        './resources/views/filament/**/*.blade.php',

        // مسارات Filament الموجودة في مجلد vendor
        './vendor/filament/**/*.blade.php',

        // مسارات ملفاتك المعتادة
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./resources/**/*.ts",
        "./resources/**/*.jsx",
        "./resources/**/*.tsx",
    ],
    theme: {
        extend: {},
    },
    plugins: [],
}
