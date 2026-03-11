<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('order_number', 50)->unique();
            $table->enum('status', ['pending','processing','shipped','delivered','cancelled','refunded'])->default('pending');
            $table->enum('payment_method', ['stripe','paypal','cod']);
            $table->enum('payment_status', ['pending','paid','failed','refunded'])->default('pending');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_amount', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->json('shipping_address');
            $table->json('billing_address')->nullable();
            $table->text('notes')->nullable();
            $table->string('coupon_code', 50)->nullable();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('paypal_order_id')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'status']);
            $table->index('payment_status');
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vendor_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->string('variant_label', 100)->nullable();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('commission_rate', 5, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->decimal('vendor_amount', 10, 2);
            $table->enum('status', ['pending','processing','shipped','delivered','cancelled'])->default('pending');
            $table->string('tracking_number')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway', 50);
            $table->string('transaction_id')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('USD');
            $table->enum('status', ['pending','success','failed','refunded'])->default('pending');
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['stripe', 'paypal', 'bank_transfer']);
            $table->enum('status', ['pending','processing','completed','failed'])->default('pending');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};