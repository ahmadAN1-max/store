<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // الأقسام الرئيسية
        $categories = [
            'NewBorn Boy (0-0 --> 6-9)' => [
                'NewBorn Boy Overall and romper',
                'NewBorn Boy Pajama',
                'NewBorn Boy Sets',
                'NewBorn Boy Short',
                'NewBorn Boy Trousers and Pants',
                'NewBorn Boy T-Shirt and Blouse',
                'NewBorn Boy Shirt',
                'NewBorn Boy Jackets',
                'NewBorn Boy Accessories',
                'NewBorn Boy Bodysuits',
            ],
            'NewBorn Girl (0-0 --> 6-9)' => [
                'NewBorn Girl Overall and romper',
                'NewBorn Girl Pajama',
                'NewBorn Girl Sets',
                'NewBorn Girl Short',
                'NewBorn Girl Trousers and Pants',
                'NewBorn Girl T-Shirt and Blouse',
                'NewBorn Girl Shirt',
                'NewBorn Girl Jackets',
                'NewBorn Girl Accessories',
                'NewBorn Girl Skirts and Dresses',
                'NewBorn Girl Bodysuits',
            ],
            'Baby Boy (6-9 --> 24-36)' => [
                'Baby Boy Overall and romper',
                'Baby Boy Pajama',
                'Baby Boy Sets',
                'Baby Boy Short',
                'Baby Boy Trousers and Pants',
                'Baby Boy T-Shirt and Blouse',
                'Baby Boy Shirt',
                'Baby Boy Jackets',
                'Baby Boy Accessories',
                'Baby Boy Bodysuits',
            ],
            'Baby Girl (6-9 --> 24-36)' => [
                'Baby Girl Overall and romper',
                'Baby Girl Pajama',
                'Baby Girl Sets',
                'Baby Girl Short',
                'Baby Girl Trousers and Pants',
                'Baby Girl T-Shirt and Blouse',
                'Baby Girl Shirt',
                'Baby Girl Jackets',
                'Baby Girl Accessories',
                'Baby Girl Skirts and Dresses',
                'Baby Girl Bodysuits',
            ],
            'Kids Boy (3y --> 13-14y)' => [
                'Kids Boy Pajama',
                'Kids Boy Sets',
                'Kids Boy Overall/Jumpsuit',
                'Kids Boy Short',
                'Kids Boy Trousers and Pants',
                'Kids Boy T-Shirt and Blouse',
                'Kids Boy Shirt',
                'Kids Boy Jackets',
                'Kids Boy Accessories',
            ],
            'Kids Girl (3y --> 13-14y)' => [
                'Kids Girl Pajama',
                'Kids Girl Sets',
                'Kids Girl Overall/Jumpsuit',
                'Kids Girl Short',
                'Kids Girl Trousers and Pants',
                'Kids Girl T-Shirt and Blouse',
                'Kids Girl Shirt',
                'Kids Girl Jackets',
                'Kids Girl Accessories',
                'Kids Girl Skirts and Dresses',
            ],
            'Junior Boy (8y --> 16y)' => [
                'Junior Boy Pajama',
                'Junior Boy Sets',
                'Junior Boy Overall/Jumpsuit',
                'Junior Boy Short',
                'Junior Boy Trousers and Pants',
                'Junior Boy T-Shirt and Blouse',
                'Junior Boy Shirt',
                'Junior Boy Jackets',
                'Junior Boy Accessories',
            ],
            'Junior Girl (8y --> 16y)' => [
                'Junior Girl Pajama',
                'Junior Girl Sets',
                'Junior Girl Overall/Jumpsuit',
                'Junior Girl Short',
                'Junior Girl Trousers and Pants',
                'Junior Girl T-Shirt and Blouse',
                'Junior Girl Shirt',
                'Junior Girl Jackets',
                'Junior Girl Accessories',
                'Junior Girl Skirts and Dresses',
            ],

        ];

        foreach ($categories as $parentName => $subs) {
            // parent
            $parent = Category::updateOrCreate(
                ['slug' => Str::slug($parentName)],
                [
                    'name' => $parentName,
                    'slug' => Str::slug($parentName),
                    'parent_id' => null,
                    'main' => 1,
                    'free_delivery' => 0,
                ]
            );

            // sub categories
            foreach ($subs as $sub) {
                Category::updateOrCreate(
                    ['slug' => Str::slug($parent->slug . '-' . $sub)],
                    [
                        'name' => $sub,
                        'slug' => Str::slug($parent->slug . '-' . $sub),
                        'parent_id' => $parent->id,
                        'main' => 0,
                        'free_delivery' => 0,
                    ]
                );
            }
        }

        // أقسام إضافية منفصلة
        $extras = [
            'Exclusive Website',
            'sale',
            'new collection',
        ];

        foreach ($extras as $ex) {
            Category::updateOrCreate(
                ['slug' => Str::slug($ex)],
                [
                    'name' => $ex,
                    'slug' => Str::slug($ex),
                    'parent_id' => null,
                    'main' => 0,
                    'free_delivery' => 0,
                ]
            );
        }
    }
}
