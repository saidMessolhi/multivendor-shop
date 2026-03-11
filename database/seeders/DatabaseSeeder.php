<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        // ── Users ──────────────────────────────────────────────
        $admin = User::firstOrCreate(['email' => 'admin@shop.com'], [
            'name'              => 'Admin User',
            'password'          => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active'         => true,
        ]);
        $admin->assignRole('admin');

        $vendorUsers = [];
        $vendorData  = [
            ['name' => 'John Smith',   'email' => 'vendor@shop.com',  'store' => 'TechZone Store',  'desc' => 'Best electronics and gadgets online.'],
            ['name' => 'Sara Lee',     'email' => 'vendor2@shop.com', 'store' => 'Fashion Hub',     'desc' => 'Trendy clothing and accessories.'],
            ['name' => 'Mike Johnson', 'email' => 'vendor3@shop.com', 'store' => 'Home & Living',   'desc' => 'Everything for your home.'],
        ];

        foreach ($vendorData as $vd) {
            $u = User::firstOrCreate(['email' => $vd['email']], [
                'name'              => $vd['name'],
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);
            $u->assignRole('vendor');
            $vendor = Vendor::firstOrCreate(['user_id' => $u->id], [
                'store_name'  => $vd['store'],
                'store_slug'  => Str::slug($vd['store']),
                'description' => $vd['desc'],
                'status'      => 'approved',
                'approved_at' => now(),
                'balance'     => rand(100, 2000),
            ]);
            $vendorUsers[] = $vendor;
        }

        $customers = [];
        $customerData = [
            ['name' => 'Alice Martin', 'email' => 'customer@shop.com'],
            ['name' => 'Bob Wilson',   'email' => 'customer2@shop.com'],
            ['name' => 'Clara Davis',  'email' => 'customer3@shop.com'],
            ['name' => 'David Brown',  'email' => 'customer4@shop.com'],
            ['name' => 'Eva Green',    'email' => 'customer5@shop.com'],
        ];
        foreach ($customerData as $cd) {
            $u = User::firstOrCreate(['email' => $cd['email']], [
                'name'              => $cd['name'],
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active'         => true,
            ]);
            $u->assignRole('customer');
            $customers[] = $u;
        }

        // ── Categories ─────────────────────────────────────────
        $categoryData = [
            ['name' => 'Electronics',  'children' => ['Phones', 'Laptops', 'Accessories']],
            ['name' => 'Clothing',     'children' => ['Men', 'Women', 'Kids']],
            ['name' => 'Home & Garden','children' => ['Furniture', 'Kitchen', 'Garden']],
            ['name' => 'Books',        'children' => ['Fiction', 'Science', 'Business']],
            ['name' => 'Sports',       'children' => ['Fitness', 'Outdoor', 'Team Sports']],
            ['name' => 'Beauty',       'children' => ['Skincare', 'Makeup', 'Haircare']],
        ];

        $allCategories = [];
        foreach ($categoryData as $i => $cd) {
            $parent = Category::firstOrCreate(['slug' => Str::slug($cd['name'])], [
                'name'       => $cd['name'],
                'is_active'  => true,
                'sort_order' => $i,
            ]);
            $allCategories[$cd['name']] = $parent;
            foreach ($cd['children'] as $j => $child) {
                $allCategories[$child] = Category::firstOrCreate(
                    ['slug' => Str::slug($child . '-' . $cd['name'])],
                    ['name' => $child, 'parent_id' => $parent->id, 'is_active' => true, 'sort_order' => $j]
                );
            }
        }

        // ── Products ───────────────────────────────────────────
        $productData = [
            ['name' => 'iPhone 15 Pro Max 256GB',     'price' => 1199.99, 'sale' => 1099.99, 'stock' => 15,  'cat' => 'Phones',     'vendor' => 0, 'featured' => true],
            ['name' => 'Samsung Galaxy S24 Ultra',    'price' => 1099.99, 'sale' => null,    'stock' => 20,  'cat' => 'Phones',     'vendor' => 0, 'featured' => true],
            ['name' => 'MacBook Pro M3 14"',           'price' => 1999.99, 'sale' => 1849.99, 'stock' => 8,   'cat' => 'Laptops',    'vendor' => 0, 'featured' => true],
            ['name' => 'Dell XPS 15 Laptop',          'price' => 1499.99, 'sale' => null,    'stock' => 10,  'cat' => 'Laptops',    'vendor' => 0, 'featured' => false],
            ['name' => 'AirPods Pro 2nd Generation',  'price' => 249.99,  'sale' => 199.99,  'stock' => 50,  'cat' => 'Accessories','vendor' => 0, 'featured' => true],
            ['name' => 'USB-C Hub 7-in-1',            'price' => 49.99,   'sale' => null,    'stock' => 100, 'cat' => 'Accessories','vendor' => 0, 'featured' => false],
            ['name' => 'Wireless Charging Pad 15W',   'price' => 39.99,   'sale' => 29.99,   'stock' => 75,  'cat' => 'Accessories','vendor' => 0, 'featured' => false],
            ['name' => 'Classic Slim Fit Jeans',      'price' => 79.99,   'sale' => 59.99,   'stock' => 60,  'cat' => 'Men',        'vendor' => 1, 'featured' => false],
            ['name' => 'Oversized Cotton Hoodie',     'price' => 64.99,   'sale' => null,    'stock' => 45,  'cat' => 'Men',        'vendor' => 1, 'featured' => true],
            ['name' => 'Floral Summer Dress',         'price' => 54.99,   'sale' => 44.99,   'stock' => 35,  'cat' => 'Women',      'vendor' => 1, 'featured' => true],
            ['name' => 'High-Waist Yoga Pants',       'price' => 44.99,   'sale' => null,    'stock' => 55,  'cat' => 'Women',      'vendor' => 1, 'featured' => false],
            ['name' => 'Kids Rainbow T-Shirt Set',    'price' => 29.99,   'sale' => 24.99,   'stock' => 40,  'cat' => 'Kids',       'vendor' => 1, 'featured' => false],
            ['name' => 'Leather Crossbody Bag',       'price' => 89.99,   'sale' => null,    'stock' => 25,  'cat' => 'Women',      'vendor' => 1, 'featured' => true],
            ['name' => 'Ergonomic Office Chair',      'price' => 349.99,  'sale' => 299.99,  'stock' => 12,  'cat' => 'Furniture',  'vendor' => 2, 'featured' => true],
            ['name' => 'Minimalist Bookshelf 5-Tier', 'price' => 189.99,  'sale' => null,    'stock' => 18,  'cat' => 'Furniture',  'vendor' => 2, 'featured' => false],
            ['name' => 'Non-Stick Cookware Set 10pc', 'price' => 129.99,  'sale' => 99.99,   'stock' => 30,  'cat' => 'Kitchen',    'vendor' => 2, 'featured' => true],
            ['name' => 'French Press Coffee Maker',   'price' => 34.99,   'sale' => null,    'stock' => 60,  'cat' => 'Kitchen',    'vendor' => 2, 'featured' => false],
            ['name' => 'Indoor Plant Pot Set 3pc',    'price' => 24.99,   'sale' => 19.99,   'stock' => 80,  'cat' => 'Garden',     'vendor' => 2, 'featured' => false],
            ['name' => 'Solar Garden Lights 8-Pack',  'price' => 39.99,   'sale' => null,    'stock' => 45,  'cat' => 'Garden',     'vendor' => 2, 'featured' => false],
        ];

        $products = [];
        foreach ($productData as $pd) {
            $vendor   = $vendorUsers[$pd['vendor']];
            $category = $allCategories[$pd['cat']] ?? null;
            $product  = Product::firstOrCreate(['slug' => Str::slug($pd['name'])], [
                'vendor_id'         => $vendor->id,
                'category_id'       => $category?->id,
                'name'              => $pd['name'],
                'short_description' => fake()->sentence(12),
                'description'       => fake()->paragraphs(3, true),
                'price'             => $pd['price'],
                'sale_price'        => $pd['sale'],
                'stock'             => $pd['stock'],
                'sku'               => strtoupper(Str::random(8)),
                'status'            => 'active',
                'is_featured'       => $pd['featured'],
                'rating_avg'        => round(rand(35, 50) / 10, 1),
                'rating_count'      => rand(5, 120),
            ]);
            $products[] = $product;
        }

        // ── Orders ─────────────────────────────────────────────
        $statuses        = ['pending', 'processing', 'shipped', 'delivered', 'delivered', 'delivered'];
        $paymentMethods  = ['stripe', 'paypal', 'cod'];
        $paymentStatuses = ['paid', 'paid', 'paid', 'pending'];

        for ($i = 0; $i < 25; $i++) {
            $customer      = $customers[array_rand($customers)];
            $orderProducts = collect($products)->random(rand(1, 3));
            $status        = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $paymentMethod = $paymentMethods[array_rand($paymentMethods)];

            $subtotal = $commissionTotal = 0;
            $itemsData = [];

            foreach ($orderProducts as $product) {
                $qty       = rand(1, 3);
                $unitPrice = $product->sale_price ?? $product->price;
                $commission = round($unitPrice * $qty * 0.10, 2);
                $subtotal        += $unitPrice * $qty;
                $commissionTotal += $commission;
                $itemsData[] = [
                    'vendor_id'          => $product->vendor_id,
                    'product_id'         => $product->id,
                    'product_name'       => $product->name,
                    'unit_price'         => $unitPrice,
                    'quantity'           => $qty,
                    'subtotal'           => $unitPrice * $qty,
                    'commission_rate'    => 10,
                    'commission_amount'  => $commission,
                    'vendor_amount'      => round(($unitPrice * $qty) - $commission, 2),
                    'status'             => $status,
                ];
            }

            $shipping = 5.99;
            $tax      = round($subtotal * 0.08, 2);
            $total    = round($subtotal + $shipping + $tax, 2);

            $order = Order::create([
                'user_id'           => $customer->id,
                'order_number'      => 'ORD-' . strtoupper(Str::random(8)),
                'status'            => $status,
                'payment_method'    => $paymentMethod,
                'payment_status'    => $paymentStatus,
                'subtotal'          => round($subtotal, 2),
                'shipping_amount'   => $shipping,
                'tax_amount'        => $tax,
                'discount_amount'   => 0,
                'commission_amount' => round($commissionTotal, 2),
                'total_amount'      => $total,
                'shipping_address'  => [
                    'first_name'     => explode(' ', $customer->name)[0],
                    'last_name'      => explode(' ', $customer->name)[1] ?? '',
                    'address_line_1' => fake()->streetAddress(),
                    'city'           => fake()->city(),
                    'postal_code'    => fake()->postcode(),
                    'country'        => fake()->country(),
                ],
                'paid_at'    => $paymentStatus === 'paid' ? now()->subDays(rand(1, 30)) : null,
                'created_at' => now()->subDays(rand(1, 60)),
            ]);

            foreach ($itemsData as $item) {
                $order->items()->create($item);
            }
        }

        $this->command->info('Seeded: 3 vendors, 5 customers, 6 categories with children, 19 products, 25 orders');
    }
}