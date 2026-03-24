<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChatbotKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        // 1️⃣ FAQs
        $faqs = DB::table('faqs')->get();
        foreach ($faqs as $faq) {
            $question_ar = json_decode($faq->question, true)['ar'] ?? '';
            $question_en = json_decode($faq->question, true)['en'] ?? '';
            $answer_ar = json_decode($faq->answer, true)['ar'] ?? '';
            $answer_en = json_decode($faq->answer, true)['en'] ?? '';

            DB::table('chatbot_knowledge')->insert([
                'source_type' => 'faq',
                'title' => "AR: $question_ar\nEN: $question_en",
                'content' => "AR: $answer_ar\nEN: $answer_en",
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 2️⃣ Products
        $products = DB::table('products')
            ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->select('products.*', 'brands.name as brand_name', 'categories.name as category_name')
            ->get();

        foreach ($products as $prod) {
            $name_ar = json_decode($prod->name, true)['ar'] ?? '';
            $name_en = json_decode($prod->name, true)['en'] ?? '';
            $desc_ar = json_decode($prod->description, true)['ar'] ?? '';
            $desc_en = json_decode($prod->description, true)['en'] ?? '';

            DB::table('chatbot_knowledge')->insert([
                'source_type' => 'product',
                'title' => "AR: $name_ar\nEN: $name_en",
                'content' => "AR: $desc_ar\nEN: $desc_en\nBrand: " . ($prod->brand_name ?? '') . "\nCategory: " . ($prod->category_name ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3️⃣ Product Variants
        $variants = DB::table('product_variants')->get();
        foreach ($variants as $variant) {
            $name_ar = json_decode($variant->name, true)['ar'] ?? '';
            $name_en = json_decode($variant->name, true)['en'] ?? '';

            DB::table('chatbot_knowledge')->insert([
                'source_type' => 'product_variant',
                'title' => "AR: $name_ar\nEN: $name_en",
                'content' => "Protocol: " . ($variant->protocol ?? '') .
                             "\nColor: " . ($variant->color ?? '') .
                             "\nSize: " . ($variant->size ?? ''),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4️⃣ Data Sheets
        $sheets = DB::table('data_sheets')->get();
        foreach ($sheets as $sheet) {
            // لو عندك عنوان JSON استخدم decode زي قبل، هنا نفترض اسم الملف
            $title = basename($sheet->file_path);
            $content = "Refer to the file at: " . $sheet->file_path;

            DB::table('chatbot_knowledge')->insert([
                'source_type' => 'data_sheet',
                'title' => $title,
                'content' => $content,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
