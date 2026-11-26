<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/*
 * Data contoh untuk mencoba modul Penjual.
 * Login: vendor@sawitsupply.test / password
 * Jalankan: php artisan db:seed --class=SellerDemoSeeder
 */
class SellerDemoSeeder extends Seeder
{
    public function run(): void
    {
        $vendor = $this->user('Toko Sawit Sejahtera', 'vendor@sawitsupply.test', 'vendor');
        $buyers = [
            $this->user('Budi Santoso', 'budi@sawitsupply.test', 'buyer'),
            $this->user('Siti Aminah', 'siti@sawitsupply.test', 'buyer'),
            $this->user('Andi Pratama', 'andi@sawitsupply.test', 'buyer'),
        ];

        $cats = [];
        foreach (['Pupuk' => 'pupuk', 'Alat Panen' => 'alat-panen', 'Alat Semprot' => 'alat-semprot', 'APD' => 'apd'] as $name => $slug) {
            $cats[$slug] = Category::firstOrCreate(['slug' => $slug], ['category_name' => $name]);
        }

        $rows = [
            ['Pupuk NPK 13-6-27 (50 kg)', 'pupuk',        330000, 40, 50000],
            ['Pupuk Urea 50 kg',          'pupuk',        280000, 4,  50000],
            ['Pupuk Dolomit 50 kg',       'pupuk',        120000, 25, 50000],
            ['Egrek Sawit Galah Panjang', 'alat-panen',   450000, 6,  3500],
            ['Dodos Sawit',               'alat-panen',   180000, 18, 2500],
            ['Sprayer Punggung 16 L',     'alat-semprot', 350000, 12, 3000],
            ['Sarung Tangan Kerja',       'apd',          25000,  9,  200],
            ['Sepatu Boot Kebun',         'apd',          140000, 22, 1200],
        ];

        $products = [];
        foreach ($rows as [$name, $slug, $price, $stock, $weight]) {
            $products[] = Product::firstOrCreate(
                ['vendor_id' => $vendor->id, 'product_name' => $name],
                ['category_id' => $cats[$slug]->id, 'price' => $price, 'stock' => $stock, 'weight_gram' => $weight,
                 'description' => 'Produk contoh untuk uji coba dashboard penjual.', 'is_active' => true]
            );
        }

        // [hari lalu, index produk, qty, status pesanan, status bayar, metode]
        $orders = [
            [0, 0, 2, 'pending_payment', 'waiting', 'Transfer Bank'],
            [0, 3, 1, 'pending_payment', 'waiting', 'Transfer Bank'],
            [1, 1, 3, 'processing',      'paid',    'Virtual Account'],
            [1, 4, 2, 'processing',      'paid',    'Transfer Bank'],
            [2, 5, 1, 'shipped',         'paid',    'Transfer Bank'],
            [3, 0, 4, 'shipped',         'paid',    'Virtual Account'],
            [4, 2, 5, 'completed',       'paid',    'Transfer Bank'],
            [5, 7, 2, 'completed',       'paid',    'Transfer Bank'],
            [6, 6, 10, 'completed',      'paid',    'Virtual Account'],
            [20, 0, 6, 'completed',      'paid',    'Transfer Bank'],
            [35, 3, 2, 'completed',      'paid',    'Transfer Bank'],
        ];

        foreach ($orders as $i => [$daysAgo, $pi, $qty, $orderStatus, $payStatus, $method]) {
            $date   = now()->subDays($daysAgo)->setTime(9 + $i % 8, 15);
            $number = 'SS-' . $date->format('ymd') . '-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT);
            if (Order::where('order_number', $number)->exists()) {
                continue;
            }

            $product = $products[$pi];
            $order = new Order([
                'order_number'    => $number,
                'buyer_id'        => $buyers[$i % 3]->id,
                'vendor_id'       => $vendor->id,
                'grand_total'     => $product->price * $qty + 25000, // + ongkir contoh
                'order_status'    => $orderStatus,
                'payment_status'  => $payStatus,
                'tracking_number' => in_array($orderStatus, ['shipped', 'completed']) ? 'JNE' . (1000000 + $i * 137) : null,
            ]);
            $order->created_at = $date;
            $order->save();

            OrderItem::create([
                'order_id' => $order->id, 'product_id' => $product->id,
                'quantity' => $qty, 'price_at_purchase' => $product->price,
            ]);

            Payment::create([
                'order_id' => $order->id, 'payment_method' => $method, 'proof_file' => null,
                'payment_date' => $payStatus === 'paid' ? $date->copy()->addHours(2) : null,
            ]);
        }
    }

    private function user(string $name, string $email, string $role): User
    {
        $u = User::firstOrNew(['email' => $email]);
        $u->forceFill(['name' => $name, 'role' => $role, 'password' => Hash::make('password')])->save();

        return $u;
    }
}
