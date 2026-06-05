<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ────────────────────────────────────────────────────────
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

        $catModels = [];
        foreach ($categories as $cat) {
            $catModels[$cat['name']] = Category::firstOrCreate(
                ['slug' => Str::slug($cat['name'])],
                ['name' => $cat['name'], 'slug' => Str::slug($cat['name']),
                 'is_active' => true, 'sort_order' => $cat['sort_order']]
            );
        }

        // ── Vendor users ──────────────────────────────────────────────────────
        $vendorData = [
            [
                'user'    => ['first_name' => 'Amina',  'last_name' => 'Rashid',  'phone' => '0711000001'],
                'profile' => ['business_name' => 'Amina Fresh Produce', 'region' => 'Dar es Salaam',
                              'district' => 'Kinondoni', 'description' => 'Fresh vegetables and fruits from local farms.',
                              'rating' => 4.8, 'is_open' => true],
            ],
            [
                'user'    => ['first_name' => 'Hassan', 'last_name' => 'Mwanga',  'phone' => '0711000002'],
                'profile' => ['business_name' => 'Mwanga Grains & Spices', 'region' => 'Dar es Salaam',
                              'district' => 'Ilala', 'description' => 'Quality grains, rice, and spices imported and local.',
                              'rating' => 4.5, 'is_open' => true],
            ],
            [
                'user'    => ['first_name' => 'Fatuma', 'last_name' => 'Omari',   'phone' => '0711000003'],
                'profile' => ['business_name' => 'Fatuma Butchery & Fish', 'region' => 'Dar es Salaam',
                              'district' => 'Temeke', 'description' => 'Fresh meat and fish daily.',
                              'rating' => 4.3, 'is_open' => false],
            ],
        ];

        $vendors = [];
        foreach ($vendorData as $data) {
            $user = User::firstOrCreate(
                ['phone' => $data['user']['phone']],
                array_merge($data['user'], [
                    'email'              => strtolower($data['user']['first_name']) . '@test.com',
                    'password'           => Hash::make('Vendor@1234'),
                    'role'               => 'vendor',
                    'status'             => 'active',
                    'profile_completed'  => true,
                    'phone_verified_at'  => now(),
                ])
            );

            $profile = VendorProfile::firstOrCreate(
                ['user_id' => $user->id],
                array_merge($data['profile'], [
                    'user_id'     => $user->id,
                    'is_approved' => true,
                    'total_reviews' => rand(20, 200),
                ])
            );

            $vendors[] = ['user' => $user, 'profile' => $profile];
        }

        // ── Products ──────────────────────────────────────────────────────────
        $products = [
            // Amina — vendor[0]
            ['name' => 'Tomatoes (1kg)',      'cat' => 'Vegetables',    'price' => 2500,  'sale_price' => 2000, 'unit' => 'kg',    'stock' => 50,  'featured' => true,  'vendor' => 0],
            ['name' => 'Spinach (Bundle)',    'cat' => 'Vegetables',    'price' => 1000,  'sale_price' => null, 'unit' => 'bundle','stock' => 30,  'featured' => false, 'vendor' => 0],
            ['name' => 'Carrots (500g)',      'cat' => 'Vegetables',    'price' => 1500,  'sale_price' => null, 'unit' => 'piece', 'stock' => 40,  'featured' => true,  'vendor' => 0],
            ['name' => 'Mango (1kg)',         'cat' => 'Fruits',        'price' => 3000,  'sale_price' => 2500, 'unit' => 'kg',    'stock' => 25,  'featured' => true,  'vendor' => 0],
            ['name' => 'Banana (Bunch)',      'cat' => 'Fruits',        'price' => 2000,  'sale_price' => null, 'unit' => 'bunch', 'stock' => 20,  'featured' => false, 'vendor' => 0],
            ['name' => 'Avocado (3 pcs)',     'cat' => 'Fruits',        'price' => 3500,  'sale_price' => 3000, 'unit' => 'piece', 'stock' => 15,  'featured' => true,  'vendor' => 0],
            // Hassan — vendor[1]
            ['name' => 'Rice Wali (5kg)',     'cat' => 'Grains & Rice', 'price' => 12000, 'sale_price' => null, 'unit' => 'bag',   'stock' => 100, 'featured' => true,  'vendor' => 1],
            ['name' => 'Maize Flour (2kg)',   'cat' => 'Grains & Rice', 'price' => 4500,  'sale_price' => 4000, 'unit' => 'bag',   'stock' => 80,  'featured' => false, 'vendor' => 1],
            ['name' => 'Black Pepper (100g)', 'cat' => 'Spices',        'price' => 3000,  'sale_price' => null, 'unit' => 'pack',  'stock' => 60,  'featured' => true,  'vendor' => 1],
            ['name' => 'Pilipili Hoho (50g)', 'cat' => 'Spices',        'price' => 1500,  'sale_price' => null, 'unit' => 'pack',  'stock' => 45,  'featured' => false, 'vendor' => 1],
            ['name' => 'Sunflower Oil (1L)',  'cat' => 'Cooking Oil',   'price' => 6000,  'sale_price' => 5500, 'unit' => 'litre', 'stock' => 70,  'featured' => true,  'vendor' => 1],
            // Fatuma — vendor[2]
            ['name' => 'Chicken (1kg)',       'cat' => 'Meat & Fish',   'price' => 9000,  'sale_price' => null, 'unit' => 'kg',    'stock' => 20,  'featured' => true,  'vendor' => 2],
            ['name' => 'Beef (500g)',         'cat' => 'Meat & Fish',   'price' => 8000,  'sale_price' => 7000, 'unit' => 'piece', 'stock' => 15,  'featured' => true,  'vendor' => 2],
            ['name' => 'Tilapia Fish (1kg)',  'cat' => 'Meat & Fish',   'price' => 7000,  'sale_price' => null, 'unit' => 'kg',    'stock' => 10,  'featured' => false, 'vendor' => 2],
            ['name' => 'Eggs (Tray 30pcs)',   'cat' => 'Dairy & Eggs',  'price' => 9500,  'sale_price' => 9000, 'unit' => 'tray',  'stock' => 50,  'featured' => true,  'vendor' => 2],
        ];

        foreach ($products as $p) {
            $category = $catModels[$p['cat']];
            $vendor   = $vendors[$p['vendor']]['user'];

            $product = Product::firstOrCreate(
                ['slug' => Str::slug($p['name'])],
                [
                    'vendor_id'    => $vendor->id,
                    'category_id'  => $category->id,
                    'name'         => $p['name'],
                    'slug'         => Str::slug($p['name']),
                    'price'        => $p['price'],
                    'sale_price'   => $p['sale_price'],
                    'unit'         => $p['unit'],
                    'stock'        => $p['stock'],
                    'is_active'    => true,
                    'is_featured'  => $p['featured'],
                    'rating'       => round(rand(38, 50) / 10, 1),
                    'total_reviews'=> rand(5, 80),
                    'total_orders' => rand(10, 300),
                    'description'  => "Fresh {$p['name']} sourced directly from local farmers and suppliers in Tanzania.",
                ]
            );
        }

        $this->command->info('✓ ' . count($categories) . ' categories, ' . count($vendors) . ' vendors, ' . count($products) . ' products seeded.');
    }
}
