<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name_ar' => 'أخبار عامة',
                'name_en' => 'General',
                'slug' => 'general',
                'icon' => 'newspaper',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name_ar' => 'سياسة',
                'name_en' => 'Politics',
                'slug' => 'politics',
                'icon' => 'landmark',
                'color' => '#8B5CF6',
                'sort_order' => 2,
            ],
            [
                'name_ar' => 'اقتصاد',
                'name_en' => 'Business',
                'slug' => 'business',
                'icon' => 'chart-line',
                'color' => '#10B981',
                'sort_order' => 3,
            ],
            [
                'name_ar' => 'رياضة',
                'name_en' => 'Sports',
                'slug' => 'sports',
                'icon' => 'futbol',
                'color' => '#F59E0B',
                'sort_order' => 4,
            ],
            [
                'name_ar' => 'تقنية',
                'name_en' => 'Technology',
                'slug' => 'technology',
                'icon' => 'microchip',
                'color' => '#06B6D4',
                'sort_order' => 5,
            ],
            [
                'name_ar' => 'ترفيه',
                'name_en' => 'Entertainment',
                'slug' => 'entertainment',
                'icon' => 'film',
                'color' => '#EC4899',
                'sort_order' => 6,
            ],
            [
                'name_ar' => 'صحة',
                'name_en' => 'Health',
                'slug' => 'health',
                'icon' => 'heart-pulse',
                'color' => '#EF4444',
                'sort_order' => 7,
            ],
            [
                'name_ar' => 'علوم',
                'name_en' => 'Science',
                'slug' => 'science',
                'icon' => 'flask',
                'color' => '#14B8A6',
                'sort_order' => 8,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                array_merge($category, ['is_active' => true])
            );
        }
    }
}



