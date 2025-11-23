<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // مهم لاستخدام استعلام التحديث

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // الخطوة 1: تحديث أي صفوف حالية بها 'services'
        // يجب أن تقوم بتغيير القيمة 'services' إلى 'products' (أو 'blogs')
        // قبل محاولة إزالة 'services' من تعريف العمود لتجنب الأخطاء.
        DB::table('categories') // <-- قم بتغيير 'اسم_جدولك_هنا'
            ->where('type', 'services')
            ->update(['type' => 'products']);

        // الخطوة 2: تعديل تعريف العمود
        Schema::table('categories', function (Blueprint $table) { // <-- قم بتغيير 'اسم_جدولك_هنا'
            $table->enum('type', ['products', 'blogs']) // القيم الجديدة فقط
                  ->default('products')
                  ->change(); // الأهم: استخدام change() لتعديل العمود الحالي
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // لإلغاء التغيير (الـ Rollback)، نقوم بإرجاع 'services' للقيم المتاحة.
        Schema::table('categories', function (Blueprint $table) { // <-- قم بتغيير 'اسم_جدولك_هنا'
            $table->enum('type', ['products', 'services', 'blogs'])
                  ->default('products')
                  ->change();
        });
    }
};
