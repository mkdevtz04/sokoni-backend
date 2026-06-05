<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Vegetables',    'sort_order' => 1],
            ['name' => 'Fruits',        'sort_order' => 2],
            ['name' => 'Grains & Rice', 'sort_order' => 3],
            ['name' => 'Meat & Fish',   'sort_order' => 4],
            ['name' => 'Dairy & Eggs',  'sort_order' => 5],
            ['name' => 'Spices',        'sort_order' => 6],
            ['name' => 'Cooking Oil',   'sort_order' => 7],
            ['name' => 'Beverages',     'sort_order' => 8],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                [
                    'name'       => $cat['name'],
                    'slug'       => Str::slug($cat['name']),
                    'is_active'  => true,
                    'sort_order' => $cat['sort_order'],
                ]
            );
        }
    }
}
