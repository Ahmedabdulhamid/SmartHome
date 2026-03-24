<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\Governorate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EgyptGovernoratesAndCitiesSeeder extends Seeder
{
    /**
     * Seed Egypt governorates and cities.
     */
    public function run(): void
    {
        foreach ($this->dataset() as $entry) {
            $governorateAr = $entry['governorate']['ar'];
            $governorateEn = $entry['governorate']['en'];
            $governorateSlug = $this->slug($governorateEn);

            $governorate = Governorate::updateOrCreate(
                ['slug' => $governorateSlug],
                [
                    'name' => ['ar' => $governorateAr, 'en' => $governorateEn],
                    'status' => 'active',
                ]
            );

            foreach ($entry['cities'] as $city) {
                $cityAr = $city['ar'];
                $cityEn = $city['en'];

                City::updateOrCreate(
                    ['slug' => $this->slug($cityEn . ' ' . $governorateEn)],
                    [
                        'name' => ['ar' => $cityAr, 'en' => $cityEn],
                        'governorate_id' => $governorate->id,
                        'status' => 'active',
                    ]
                );
            }
        }
    }

    /**
     * @return array<int, array{
     *   governorate: array{ar: string, en: string},
     *   cities: array<int, array{ar: string, en: string}>
     * }>
     */
    private function dataset(): array
    {
        return [
            [
                'governorate' => ['ar' => 'القاهرة', 'en' => 'Cairo'],
                'cities' => [
                    ['ar' => 'القاهرة', 'en' => 'Cairo'],
                    ['ar' => 'القاهرة الجديدة', 'en' => 'New Cairo'],
                    ['ar' => 'مدينة نصر', 'en' => 'Nasr City'],
                    ['ar' => 'مصر الجديدة', 'en' => 'Heliopolis'],
                    ['ar' => 'المعادي', 'en' => 'Maadi'],
                    ['ar' => 'حلوان', 'en' => 'Helwan'],
                    ['ar' => 'الشروق', 'en' => 'El Shorouk'],
                    ['ar' => 'بدر', 'en' => 'Badr City'],
                    ['ar' => 'مدينة 15 مايو', 'en' => '15 May City'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الجيزة', 'en' => 'Giza'],
                'cities' => [
                    ['ar' => 'الجيزة', 'en' => 'Giza'],
                    ['ar' => '6 أكتوبر', 'en' => '6th of October'],
                    ['ar' => 'الشيخ زايد', 'en' => 'Sheikh Zayed'],
                    ['ar' => 'الدقي', 'en' => 'Dokki'],
                    ['ar' => 'المهندسين', 'en' => 'Mohandessin'],
                    ['ar' => 'الهرم', 'en' => 'Haram'],
                    ['ar' => 'إمبابة', 'en' => 'Imbaba'],
                    ['ar' => 'البدرشين', 'en' => 'El Badrashin'],
                    ['ar' => 'العياط', 'en' => 'El Ayyat'],
                    ['ar' => 'الصف', 'en' => 'El Saf'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الإسكندرية', 'en' => 'Alexandria'],
                'cities' => [
                    ['ar' => 'الإسكندرية', 'en' => 'Alexandria'],
                    ['ar' => 'برج العرب', 'en' => 'Borg El Arab'],
                    ['ar' => 'برج العرب الجديدة', 'en' => 'New Borg El Arab'],
                    ['ar' => 'العجمي', 'en' => 'Agami'],
                    ['ar' => 'المنتزه', 'en' => 'Montaza'],
                    ['ar' => 'سيدي جابر', 'en' => 'Sidi Gaber'],
                    ['ar' => 'ميامي', 'en' => 'Miami'],
                    ['ar' => 'سموحة', 'en' => 'Smouha'],
                    ['ar' => 'العامرية', 'en' => 'El Amreya'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الدقهلية', 'en' => 'Dakahlia'],
                'cities' => [
                    ['ar' => 'المنصورة', 'en' => 'Mansoura'],
                    ['ar' => 'طلخا', 'en' => 'Talkha'],
                    ['ar' => 'ميت غمر', 'en' => 'Mit Ghamr'],
                    ['ar' => 'دكرنس', 'en' => 'Dekernes'],
                    ['ar' => 'أجا', 'en' => 'Aga'],
                    ['ar' => 'السنبلاوين', 'en' => 'El Senbellawein'],
                    ['ar' => 'بلقاس', 'en' => 'Belqas'],
                    ['ar' => 'شربين', 'en' => 'Sherbin'],
                    ['ar' => 'جمصة', 'en' => 'Gamasa'],
                ],
            ],
            [
                'governorate' => ['ar' => 'البحر الأحمر', 'en' => 'Red Sea'],
                'cities' => [
                    ['ar' => 'الغردقة', 'en' => 'Hurghada'],
                    ['ar' => 'سفاجا', 'en' => 'Safaga'],
                    ['ar' => 'القصير', 'en' => 'Quseir'],
                    ['ar' => 'مرسى علم', 'en' => 'Marsa Alam'],
                    ['ar' => 'رأس غارب', 'en' => 'Ras Gharib'],
                    ['ar' => 'شلاتين', 'en' => 'Shalateen'],
                ],
            ],
            [
                'governorate' => ['ar' => 'البحيرة', 'en' => 'Beheira'],
                'cities' => [
                    ['ar' => 'دمنهور', 'en' => 'Damanhur'],
                    ['ar' => 'كفر الدوار', 'en' => 'Kafr El Dawwar'],
                    ['ar' => 'رشيد', 'en' => 'Rashid'],
                    ['ar' => 'إدكو', 'en' => 'Edku'],
                    ['ar' => 'أبو حمص', 'en' => 'Abu Hummus'],
                    ['ar' => 'الدلنجات', 'en' => 'Delengat'],
                    ['ar' => 'حوش عيسى', 'en' => 'Hosh Issa'],
                    ['ar' => 'إيتاي البارود', 'en' => 'Itay El Barud'],
                    ['ar' => 'كوم حمادة', 'en' => 'Kom Hamada'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الفيوم', 'en' => 'Fayoum'],
                'cities' => [
                    ['ar' => 'الفيوم', 'en' => 'Faiyum'],
                    ['ar' => 'سنورس', 'en' => 'Senuris'],
                    ['ar' => 'طامية', 'en' => 'Tamiya'],
                    ['ar' => 'إطسا', 'en' => 'Etsa'],
                    ['ar' => 'إبشواي', 'en' => 'Ibshaway'],
                    ['ar' => 'يوسف الصديق', 'en' => 'Yousef El Seddiq'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الغربية', 'en' => 'Gharbia'],
                'cities' => [
                    ['ar' => 'طنطا', 'en' => 'Tanta'],
                    ['ar' => 'المحلة الكبرى', 'en' => 'El Mahalla El Kubra'],
                    ['ar' => 'كفر الزيات', 'en' => 'Kafr El Zayat'],
                    ['ar' => 'زفتى', 'en' => 'Zefta'],
                    ['ar' => 'سمنود', 'en' => 'Samannoud'],
                    ['ar' => 'قطور', 'en' => 'Qutur'],
                    ['ar' => 'بسيون', 'en' => 'Basyoun'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الإسماعيلية', 'en' => 'Ismailia'],
                'cities' => [
                    ['ar' => 'الإسماعيلية', 'en' => 'Ismailia'],
                    ['ar' => 'فايد', 'en' => 'Fayed'],
                    ['ar' => 'القنطرة شرق', 'en' => 'Qantara East'],
                    ['ar' => 'القنطرة غرب', 'en' => 'Qantara West'],
                    ['ar' => 'أبو صوير', 'en' => 'Abu Suwayr'],
                    ['ar' => 'التل الكبير', 'en' => 'Tell El Kebir'],
                    ['ar' => 'القصاصين', 'en' => 'Kasaseen'],
                ],
            ],
            [
                'governorate' => ['ar' => 'المنوفية', 'en' => 'Menofia'],
                'cities' => [
                    ['ar' => 'شبين الكوم', 'en' => 'Shebin El Kom'],
                    ['ar' => 'منوف', 'en' => 'Menouf'],
                    ['ar' => 'أشمون', 'en' => 'Ashmoun'],
                    ['ar' => 'قويسنا', 'en' => 'Quesna'],
                    ['ar' => 'تلا', 'en' => 'Tala'],
                    ['ar' => 'بركة السبع', 'en' => 'Berket El Sab'],
                    ['ar' => 'الباجور', 'en' => 'El Bagour'],
                    ['ar' => 'السادات', 'en' => 'Sadat City'],
                ],
            ],
            [
                'governorate' => ['ar' => 'المنيا', 'en' => 'Minya'],
                'cities' => [
                    ['ar' => 'المنيا', 'en' => 'Minya'],
                    ['ar' => 'ملوي', 'en' => 'Mallawi'],
                    ['ar' => 'مغاغة', 'en' => 'Maghagha'],
                    ['ar' => 'بني مزار', 'en' => 'Beni Mazar'],
                    ['ar' => 'سمالوط', 'en' => 'Samalut'],
                    ['ar' => 'أبو قرقاص', 'en' => 'Abu Qurqas'],
                    ['ar' => 'مطاي', 'en' => 'Matai'],
                    ['ar' => 'دير مواس', 'en' => 'Deir Mawas'],
                    ['ar' => 'المنيا الجديدة', 'en' => 'New Minya'],
                ],
            ],
            [
                'governorate' => ['ar' => 'القليوبية', 'en' => 'Qalyubia'],
                'cities' => [
                    ['ar' => 'بنها', 'en' => 'Banha'],
                    ['ar' => 'قليوب', 'en' => 'Qalyub'],
                    ['ar' => 'شبرا الخيمة', 'en' => 'Shubra El Kheima'],
                    ['ar' => 'الخانكة', 'en' => 'El Khanka'],
                    ['ar' => 'كفر شكر', 'en' => 'Kafr Shukr'],
                    ['ar' => 'طوخ', 'en' => 'Tukh'],
                    ['ar' => 'قها', 'en' => 'Qaha'],
                    ['ar' => 'العبور', 'en' => 'Obour'],
                    ['ar' => 'الخصوص', 'en' => 'Khusus'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الوادي الجديد', 'en' => 'New Valley'],
                'cities' => [
                    ['ar' => 'الخارجة', 'en' => 'Kharga'],
                    ['ar' => 'الداخلة', 'en' => 'Dakhla'],
                    ['ar' => 'الفرافرة', 'en' => 'Farafra'],
                    ['ar' => 'باريس', 'en' => 'Paris'],
                    ['ar' => 'بلاط', 'en' => 'Balat'],
                    ['ar' => 'بلاط القديمة', 'en' => 'Old Balat'],
                ],
            ],
            [
                'governorate' => ['ar' => 'السويس', 'en' => 'Suez'],
                'cities' => [
                    ['ar' => 'السويس', 'en' => 'Suez'],
                    ['ar' => 'الأربعين', 'en' => 'Arbaeen'],
                    ['ar' => 'الجناين', 'en' => 'Ganayen'],
                    ['ar' => 'عتاقة', 'en' => 'Ataqa'],
                    ['ar' => 'فيصل', 'en' => 'Faisal'],
                ],
            ],
            [
                'governorate' => ['ar' => 'أسوان', 'en' => 'Aswan'],
                'cities' => [
                    ['ar' => 'أسوان', 'en' => 'Aswan'],
                    ['ar' => 'كوم أمبو', 'en' => 'Kom Ombo'],
                    ['ar' => 'إدفو', 'en' => 'Edfu'],
                    ['ar' => 'دراو', 'en' => 'Daraw'],
                    ['ar' => 'نصر النوبة', 'en' => 'Nasr El Nuba'],
                    ['ar' => 'أبو سمبل', 'en' => 'Abu Simbel'],
                ],
            ],
            [
                'governorate' => ['ar' => 'أسيوط', 'en' => 'Assiut'],
                'cities' => [
                    ['ar' => 'أسيوط', 'en' => 'Assiut'],
                    ['ar' => 'ديروط', 'en' => 'Dairut'],
                    ['ar' => 'منفلوط', 'en' => 'Manfalut'],
                    ['ar' => 'القوصية', 'en' => 'Qusiya'],
                    ['ar' => 'أبنوب', 'en' => 'Abnub'],
                    ['ar' => 'أبو تيج', 'en' => 'Abu Tig'],
                    ['ar' => 'البداري', 'en' => 'El Badari'],
                    ['ar' => 'ساحل سليم', 'en' => 'Sahel Selim'],
                ],
            ],
            [
                'governorate' => ['ar' => 'بني سويف', 'en' => 'Beni Suef'],
                'cities' => [
                    ['ar' => 'بني سويف', 'en' => 'Beni Suef'],
                    ['ar' => 'الواسطى', 'en' => 'El Wasta'],
                    ['ar' => 'ناصر', 'en' => 'Nasser'],
                    ['ar' => 'إهناسيا', 'en' => 'Ehnasia'],
                    ['ar' => 'ببا', 'en' => 'Biba'],
                    ['ar' => 'سمسطا', 'en' => 'Somosta'],
                    ['ar' => 'الفشن', 'en' => 'El Fashn'],
                ],
            ],
            [
                'governorate' => ['ar' => 'بورسعيد', 'en' => 'Port Said'],
                'cities' => [
                    ['ar' => 'بورسعيد', 'en' => 'Port Said'],
                    ['ar' => 'بورفؤاد', 'en' => 'Port Fouad'],
                    ['ar' => 'العرب', 'en' => 'El Arab'],
                    ['ar' => 'الضواحي', 'en' => 'El Dawahy'],
                    ['ar' => 'المناخ', 'en' => 'El Manakh'],
                    ['ar' => 'الزهور', 'en' => 'El Zohour'],
                ],
            ],
            [
                'governorate' => ['ar' => 'دمياط', 'en' => 'Damietta'],
                'cities' => [
                    ['ar' => 'دمياط', 'en' => 'Damietta'],
                    ['ar' => 'دمياط الجديدة', 'en' => 'New Damietta'],
                    ['ar' => 'رأس البر', 'en' => 'Ras El Bar'],
                    ['ar' => 'كفر سعد', 'en' => 'Kafr Saad'],
                    ['ar' => 'فارسكور', 'en' => 'Faraskur'],
                    ['ar' => 'الزرقا', 'en' => 'Zarqa'],
                    ['ar' => 'كفر البطيخ', 'en' => 'Kafr El Battikh'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الشرقية', 'en' => 'Sharqia'],
                'cities' => [
                    ['ar' => 'الزقازيق', 'en' => 'Zagazig'],
                    ['ar' => 'العاشر من رمضان', 'en' => '10th of Ramadan'],
                    ['ar' => 'بلبيس', 'en' => 'Belbeis'],
                    ['ar' => 'منيا القمح', 'en' => 'Minya El Qamh'],
                    ['ar' => 'أبو حماد', 'en' => 'Abu Hammad'],
                    ['ar' => 'أبو كبير', 'en' => 'Abu Kebir'],
                    ['ar' => 'فاقوس', 'en' => 'Faqous'],
                    ['ar' => 'الحسينية', 'en' => 'Husseiniya'],
                    ['ar' => 'كفر صقر', 'en' => 'Kafr Saqr'],
                    ['ar' => 'ديرب نجم', 'en' => 'Derb Negm'],
                ],
            ],
            [
                'governorate' => ['ar' => 'جنوب سيناء', 'en' => 'South Sinai'],
                'cities' => [
                    ['ar' => 'الطور', 'en' => 'El Tor'],
                    ['ar' => 'شرم الشيخ', 'en' => 'Sharm El Sheikh'],
                    ['ar' => 'دهب', 'en' => 'Dahab'],
                    ['ar' => 'نويبع', 'en' => 'Nuweiba'],
                    ['ar' => 'سانت كاترين', 'en' => 'Saint Catherine'],
                    ['ar' => 'طابا', 'en' => 'Taba'],
                    ['ar' => 'رأس سدر', 'en' => 'Ras Sedr'],
                    ['ar' => 'أبو رديس', 'en' => 'Abu Redis'],
                ],
            ],
            [
                'governorate' => ['ar' => 'كفر الشيخ', 'en' => 'Kafr El Sheikh'],
                'cities' => [
                    ['ar' => 'كفر الشيخ', 'en' => 'Kafr El Sheikh'],
                    ['ar' => 'دسوق', 'en' => 'Desouk'],
                    ['ar' => 'فوه', 'en' => 'Fuwwah'],
                    ['ar' => 'بلطيم', 'en' => 'Baltim'],
                    ['ar' => 'سيدي سالم', 'en' => 'Sidi Salem'],
                    ['ar' => 'مطوبس', 'en' => 'Metoubes'],
                    ['ar' => 'قلين', 'en' => 'Qallin'],
                    ['ar' => 'الرياض', 'en' => 'Riyadh'],
                    ['ar' => 'الحامول', 'en' => 'El Hamoul'],
                ],
            ],
            [
                'governorate' => ['ar' => 'مطروح', 'en' => 'Matrouh'],
                'cities' => [
                    ['ar' => 'مرسى مطروح', 'en' => 'Marsa Matrouh'],
                    ['ar' => 'العلمين', 'en' => 'El Alamein'],
                    ['ar' => 'الضبعة', 'en' => 'El Dabaa'],
                    ['ar' => 'السلوم', 'en' => 'Sallum'],
                    ['ar' => 'سيدي براني', 'en' => 'Sidi Barrani'],
                    ['ar' => 'سيوة', 'en' => 'Siwa'],
                ],
            ],
            [
                'governorate' => ['ar' => 'الأقصر', 'en' => 'Luxor'],
                'cities' => [
                    ['ar' => 'الأقصر', 'en' => 'Luxor'],
                    ['ar' => 'إسنا', 'en' => 'Esna'],
                    ['ar' => 'أرمنت', 'en' => 'Armant'],
                    ['ar' => 'القرنة', 'en' => 'Qurna'],
                    ['ar' => 'طيبة', 'en' => 'Tiba'],
                ],
            ],
            [
                'governorate' => ['ar' => 'قنا', 'en' => 'Qena'],
                'cities' => [
                    ['ar' => 'قنا', 'en' => 'Qena'],
                    ['ar' => 'قوص', 'en' => 'Qus'],
                    ['ar' => 'نجع حمادي', 'en' => 'Nag Hammadi'],
                    ['ar' => 'دشنا', 'en' => 'Dishna'],
                    ['ar' => 'أبو تشت', 'en' => 'Abu Tesht'],
                    ['ar' => 'فرشوط', 'en' => 'Farshut'],
                    ['ar' => 'قفط', 'en' => 'Qift'],
                    ['ar' => 'نقادة', 'en' => 'Naqada'],
                ],
            ],
            [
                'governorate' => ['ar' => 'شمال سيناء', 'en' => 'North Sinai'],
                'cities' => [
                    ['ar' => 'العريش', 'en' => 'Arish'],
                    ['ar' => 'الشيخ زويد', 'en' => 'Sheikh Zuweid'],
                    ['ar' => 'رفح', 'en' => 'Rafah'],
                    ['ar' => 'بئر العبد', 'en' => 'Bir El Abd'],
                    ['ar' => 'نخل', 'en' => 'Nakhl'],
                    ['ar' => 'الحسنة', 'en' => 'Hasana'],
                ],
            ],
            [
                'governorate' => ['ar' => 'سوهاج', 'en' => 'Sohag'],
                'cities' => [
                    ['ar' => 'سوهاج', 'en' => 'Sohag'],
                    ['ar' => 'أخميم', 'en' => 'Akhmim'],
                    ['ar' => 'جرجا', 'en' => 'Girga'],
                    ['ar' => 'طهطا', 'en' => 'Tahta'],
                    ['ar' => 'طما', 'en' => 'Tima'],
                    ['ar' => 'البلينا', 'en' => 'El Balyana'],
                    ['ar' => 'دار السلام', 'en' => 'Dar El Salam'],
                    ['ar' => 'جهينة', 'en' => 'Juhayna'],
                    ['ar' => 'المراغة', 'en' => 'El Maragha'],
                ],
            ],
        ];
    }

    private function slug(string $value): string
    {
        return Str::of($value)
            ->replace("'", '')
            ->slug('-')
            ->toString();
    }
}
