<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            [
                'name_ar' => 'مصر',
                'name_en' => 'Egypt',
                'slug' => 'egypt',
                'code' => 'EG',
                'flag' => '🇪🇬',
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'السعودية',
                'name_en' => 'Saudi Arabia',
                'slug' => 'saudi-arabia',
                'code' => 'SA',
                'flag' => '🇸🇦',
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'الإمارات',
                'name_en' => 'UAE',
                'slug' => 'uae',
                'code' => 'AE',
                'flag' => '🇦🇪',
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'الكويت',
                'name_en' => 'Kuwait',
                'slug' => 'kuwait',
                'code' => 'KW',
                'flag' => '🇰🇼',
                'sort_order' => 4,
            ],
            [
                'name_ar' => 'قطر',
                'name_en' => 'Qatar',
                'slug' => 'qatar',
                'code' => 'QA',
                'flag' => '🇶🇦',
                'sort_order' => 5,
            ],
            [
                'name_ar' => 'البحرين',
                'name_en' => 'Bahrain',
                'slug' => 'bahrain',
                'code' => 'BH',
                'flag' => '🇧🇭',
                'sort_order' => 6,
            ],
            [
                'name_ar' => 'عُمان',
                'name_en' => 'Oman',
                'slug' => 'oman',
                'code' => 'OM',
                'flag' => '🇴🇲',
                'sort_order' => 7,
            ],
            [
                'name_ar' => 'الأردن',
                'name_en' => 'Jordan',
                'slug' => 'jordan',
                'code' => 'JO',
                'flag' => '🇯🇴',
                'sort_order' => 8,
            ],
            [
                'name_ar' => 'لبنان',
                'name_en' => 'Lebanon',
                'slug' => 'lebanon',
                'code' => 'LB',
                'flag' => '🇱🇧',
                'sort_order' => 9,
            ],
            [
                'name_ar' => 'المغرب',
                'name_en' => 'Morocco',
                'slug' => 'morocco',
                'code' => 'MA',
                'flag' => '🇲🇦',
                'sort_order' => 10,
            ],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['slug' => $country['slug']],
                array_merge($country, ['is_active' => true])
            );
        }
    }
}


