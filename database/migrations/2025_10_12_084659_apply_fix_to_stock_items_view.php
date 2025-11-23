<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE OR REPLACE VIEW stock_items AS
            (
                -- 1. المنتجات البسيطة (التي ليس لها متغيرات)
                SELECT
                    p.id AS entity_id,
                    'App\\\Models\\\Product' AS entity_type,
                    p.name AS item_name,

                    p.quantity AS current_quantity
                FROM products p
                WHERE p.quantity IS NOT NULL
                  -- هذا الشرط يضمن أن المنتج الأب لا يظهر إذا كان لديه متغيرات
                  AND NOT EXISTS (
                    SELECT 1
                    FROM product_variants pv
                    WHERE pv.product_id = p.id
                )
            )
            UNION ALL
            (
                -- 2. المتغيرات (وحدات المخزون الفعلية)
                SELECT
                    pv.id AS entity_id,
                    'App\\\Models\\\ProductVariant' AS entity_type,
                    CONCAT(p.name, ' - ', pv.name) AS item_name,

                    pv.quantity AS current_quantity
                FROM product_variants pv
                JOIN products p ON p.id = pv.product_id
            )
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       DB::statement("DROP VIEW IF EXISTS stock_items");
    }
};
