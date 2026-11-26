<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Tabel sesuai ERD proposal. Setiap tabel dicek dulu (hasTable/hasColumn)
 * supaya tidak error kalau Ahmad sudah membuatnya di migration lain.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $t) {
                $t->enum('role', ['buyer', 'vendor', 'admin'])->default('buyer')->after('password');
            });
        }

        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $t) {
                $t->id();
                $t->string('category_name');
                $t->string('slug')->unique();
                $t->string('icon')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $t) {
                $t->id();
                $t->foreignId('vendor_id')->constrained('users')->cascadeOnDelete();
                $t->foreignId('category_id')->constrained('categories');
                $t->string('product_name');
                $t->text('description')->nullable();
                $t->decimal('price', 12, 2);
                $t->unsignedInteger('stock')->default(0);
                $t->unsignedInteger('weight_gram')->default(1000);
                $t->string('image')->nullable();
                $t->boolean('is_active')->default(true);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('orders')) {
            Schema::create('orders', function (Blueprint $t) {
                $t->id();
                $t->string('order_number')->unique();
                $t->foreignId('buyer_id')->constrained('users');
                $t->foreignId('vendor_id')->constrained('users');
                $t->decimal('grand_total', 12, 2);
                $t->enum('order_status', ['pending_payment', 'processing', 'shipped', 'completed', 'cancelled'])->default('pending_payment');
                $t->enum('payment_status', ['unpaid', 'waiting', 'paid', 'rejected'])->default('unpaid');
                $t->string('tracking_number')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('order_items')) {
            Schema::create('order_items', function (Blueprint $t) {
                $t->id();
                $t->foreignId('order_id')->constrained()->cascadeOnDelete();
                $t->foreignId('product_id')->constrained();
                $t->unsignedInteger('quantity');
                $t->decimal('price_at_purchase', 12, 2);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $t) {
                $t->id();
                $t->foreignId('order_id')->constrained()->cascadeOnDelete();
                $t->string('payment_method');
                $t->string('proof_file')->nullable();
                $t->dateTime('payment_date')->nullable();
                $t->timestamps();
            });
        }
    }

    public function down(): void
    {
        // sengaja dikosongkan agar tabel milik tim tidak ikut terhapus
    }
};
