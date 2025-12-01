<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use App\Models\Source;
use Illuminate\Database\Seeder;

class SourceSeeder extends Seeder
{
    public function run(): void
    {
        $egypt = Country::where('slug', 'egypt')->first();
        $saudiArabia = Country::where('slug', 'saudi-arabia')->first();
        $uae = Country::where('slug', 'uae')->first();

        $general = Category::where('slug', 'general')->first();
        $sports = Category::where('slug', 'sports')->first();
        $technology = Category::where('slug', 'technology')->first();

        $sources = [
            // Egypt Sources
            [
                'name_ar' => 'اليوم السابع',
                'name_en' => 'Youm7',
                'slug' => 'youm7',
                'rss_url' => 'https://www.youm7.com/rss/SectionRss',
                'website_url' => 'https://www.youm7.com',
                'country_id' => $egypt?->id,
                'category_id' => $general?->id,
                'is_breaking_source' => true,
                'language' => 'ar',
            ],
            [
                'name_ar' => 'مصراوي',
                'name_en' => 'Masrawy',
                'slug' => 'masrawy',
                'rss_url' => 'https://www.masrawy.com/rss/rssfeeds',
                'website_url' => 'https://www.masrawy.com',
                'country_id' => $egypt?->id,
                'category_id' => $general?->id,
                'language' => 'ar',
            ],
            [
                'name_ar' => 'الأهرام',
                'name_en' => 'Al-Ahram',
                'slug' => 'alahram',
                'rss_url' => 'https://gate.ahram.org.eg/rss',
                'website_url' => 'https://gate.ahram.org.eg',
                'country_id' => $egypt?->id,
                'category_id' => $general?->id,
                'language' => 'ar',
            ],
            
            // Saudi Arabia Sources
            [
                'name_ar' => 'العربية',
                'name_en' => 'Al Arabiya',
                'slug' => 'alarabiya',
                'rss_url' => 'https://www.alarabiya.net/feed/rss2/ar.xml',
                'website_url' => 'https://www.alarabiya.net',
                'country_id' => $saudiArabia?->id,
                'category_id' => $general?->id,
                'is_breaking_source' => true,
                'language' => 'ar',
            ],
            [
                'name_ar' => 'سبق',
                'name_en' => 'Sabq',
                'slug' => 'sabq',
                'rss_url' => 'https://sabq.org/rss',
                'website_url' => 'https://sabq.org',
                'country_id' => $saudiArabia?->id,
                'category_id' => $general?->id,
                'language' => 'ar',
            ],
            
            // UAE Sources
            [
                'name_ar' => 'الإمارات اليوم',
                'name_en' => 'Emaratalyoum',
                'slug' => 'emaratalyoum',
                'rss_url' => 'https://www.emaratalyoum.com/rss',
                'website_url' => 'https://www.emaratalyoum.com',
                'country_id' => $uae?->id,
                'category_id' => $general?->id,
                'language' => 'ar',
            ],
            [
                'name_ar' => 'البيان',
                'name_en' => 'Al Bayan',
                'slug' => 'albayan',
                'rss_url' => 'https://www.albayan.ae/rss',
                'website_url' => 'https://www.albayan.ae',
                'country_id' => $uae?->id,
                'category_id' => $general?->id,
                'language' => 'ar',
            ],
            
            // Sports Sources
            [
                'name_ar' => 'يلا كورة',
                'name_en' => 'Yalla Kora',
                'slug' => 'yallakora',
                'rss_url' => 'https://www.yallakora.com/rss',
                'website_url' => 'https://www.yallakora.com',
                'country_id' => $egypt?->id,
                'category_id' => $sports?->id,
                'language' => 'ar',
            ],
            
            // Tech Sources
            [
                'name_ar' => 'البوابة العربية للأخبار التقنية',
                'name_en' => 'Aitnews',
                'slug' => 'aitnews',
                'rss_url' => 'https://aitnews.com/feed/',
                'website_url' => 'https://aitnews.com',
                'country_id' => $saudiArabia?->id,
                'category_id' => $technology?->id,
                'language' => 'ar',
            ],
            [
                'name_ar' => 'عرب هاردوير',
                'name_en' => 'Arabhardware',
                'slug' => 'arabhardware',
                'rss_url' => 'https://arabhardware.net/feed/',
                'website_url' => 'https://arabhardware.net',
                'country_id' => $egypt?->id,
                'category_id' => $technology?->id,
                'language' => 'ar',
            ],
        ];

        foreach ($sources as $source) {
            if ($source['country_id']) {
                Source::updateOrCreate(
                    ['slug' => $source['slug']],
                    array_merge($source, [
                        'is_active' => true,
                        'fetch_interval_seconds' => 300,
                    ])
                );
            }
        }
    }
}


