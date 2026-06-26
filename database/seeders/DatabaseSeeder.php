<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Comment;
use App\Models\Like;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Users (100 users)
        $this->command->info('Seeding users...');
        User::factory(100)->create();

        // Add a test user for easy login
        User::factory()->create([
            'name' => 'مستخدم تجريبي',
            'phone' => '+966500000000',
            'otp_code' => '123456',
            'otp_expires_at' => now()->addYears(1),
            'phone_verified_at' => now(),
        ]);

        $users = User::all();

        // 2. Seed Categories
        $this->command->info('Seeding categories...');
        $rootCategoriesData = [
            ['name' => 'الإلكترونيات', 'slug' => 'electronics'],
            ['name' => 'الملابس والأزياء', 'slug' => 'fashion-clothing'],
            ['name' => 'المنزل والمطبخ', 'slug' => 'home-kitchen'],
            ['name' => 'الكتب والروايات', 'slug' => 'books-novels'],
            ['name' => 'العناية والجمال', 'slug' => 'beauty-care'],
            ['name' => 'الرياضة والأنشطة الخارجية', 'slug' => 'sports-outdoors'],
            ['name' => 'الألعاب والترفيه', 'slug' => 'toys-entertainment'],
            ['name' => 'السيارات ومستلزماتها', 'slug' => 'automotive-accessories'],
            ['name' => 'البقالة والأطعمة', 'slug' => 'grocery-food'],
            ['name' => 'الصحة والعافية', 'slug' => 'health-wellness'],
        ];

        $subCategories = [];
        foreach ($rootCategoriesData as $cat) {
            $parent = Category::create([
                'parent_id' => null,
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'description' => "كل ما يخص قسم " . $cat['name'],
                'image' => fake()->imageUrl(640, 480, 'business', true),
            ]);

            // Add 3 subcategories for each root category
            for ($i = 1; $i <= 3; $i++) {
                $subNames = [
                    1 => 'قسم فرعي أ',
                    2 => 'قسم فرعي ب',
                    3 => 'قسم فرعي ج',
                ];
                $subName = $parent->name . ' - ' . $subNames[$i];
                $subSlug = $parent->slug . '-sub-' . $i;

                $subCategories[] = Category::create([
                    'parent_id' => $parent->id,
                    'name' => $subName,
                    'slug' => $subSlug,
                    'description' => "تصفح المنتجات في " . $subName,
                    'image' => fake()->imageUrl(640, 480, 'abstract', true),
                ]);
            }
        }

        // 3. Seed Products (500 products)
        $this->command->info('Seeding products...');
        
        // Disable event listeners to speed up seeding if any
        Product::withoutEvents(function () use ($subCategories) {
            for ($i = 0; $i < 500; $i++) {
                $subCategory = fake()->randomElement($subCategories);
                Product::factory()->create([
                    'category_id' => $subCategory->id,
                ]);
            }
        });

        $products = Product::all();

        // 4. Seed Comments (1000 comments)
        $this->command->info('Seeding comments...');
        Comment::factory(1000)->create();

        // 5. Seed Likes (1500 unique user-product likes)
        $this->command->info('Seeding likes...');
        $likesCount = 1500;
        $createdLikes = 0;
        $likedPairs = [];
        
        while ($createdLikes < $likesCount) {
            $user = $users->random();
            $product = $products->random();
            $key = "{$user->id}-{$product->id}";
            
            if (!isset($likedPairs[$key])) {
                $likedPairs[$key] = true;
                Like::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
                $createdLikes++;
            }
        }

        // 6. Seed Carts (200 unique active carts)
        $this->command->info('Seeding active carts...');
        $cartsCount = 200;
        $createdCarts = 0;
        $cartPairs = [];
        
        while ($createdCarts < $cartsCount) {
            $user = $users->random();
            $product = $products->random();
            $key = "{$user->id}-{$product->id}";
            
            if (!isset($cartPairs[$key])) {
                $cartPairs[$key] = true;
                Cart::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 5),
                ]);
                $createdCarts++;
            }
        }

        // 7. Seed Orders and their products (150 orders)
        $this->command->info('Seeding orders...');
        $orders = Order::factory(150)->create();
        
        foreach ($orders as $order) {
            // Each order will have between 1 to 5 items
            $itemCount = rand(1, 5);
            $orderProducts = $products->random($itemCount);
            
            $total = 0;
            foreach ($orderProducts as $product) {
                $qty = rand(1, 4);
                $price = $product->price;
                $total += $qty * $price;
                
                OrderProduct::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'price' => $price,
                ]);
            }
            
            // Update total price of the order
            $order->update(['total' => $total]);
        }

        $this->command->info('Database seeding completed successfully!');
    }
}
