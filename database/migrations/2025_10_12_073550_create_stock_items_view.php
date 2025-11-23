<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 🚨 هذا هو الأمر الصحيح لإنشاء الـ View
        // ... داخل دالة up():

        DB::statement("
    CREATE VIEW stock_items AS
    (
        -- 1. المنتجات البسيطة
        SELECT
            id AS entity_id,
            'App\\\Models\\\Product' AS entity_type,
            name AS item_name,
            -- ... (أضف الأعمدة المفقودة التي كانت موجودة سابقًا مثل SKU، إذا كنت تحتاجها)
            quantity AS current_quantity
        FROM products WHERE quantity IS NOT NULL
    )
    UNION ALL
    (
        -- 2. المتغيرات
        SELECT
            pv.id AS entity_id,
            'App\\\Models\\\ProductVariant' AS entity_type,
            -- 🚨 تم تعديل pv.attributes_string إلى pv.name (افترضنا وجود عمود اسم المتغير)
            CONCAT(p.name, ' - ', pv.name) AS item_name,

            -- ... (أضف الأعمدة المفقودة التي كانت موجودة سابقًا مثل SKU، إذا كنت تحتاجها)
            pv.quantity AS current_quantity
        FROM product_variants pv
        JOIN products p ON p.id = pv.product_id
    )
");
        // ...
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لحذف الـ View عند التراجع عن الهجرة
        DB::statement('DROP VIEW IF EXISTS stock_items');
    }
};
