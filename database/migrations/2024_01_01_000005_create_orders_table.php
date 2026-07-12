<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('order_number')->unique();
            $table->string('tracking_number')->nullable()->unique();
            
            // Amounts
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_fee', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            
            // Payment
            $table->string('payment_method');
            $table->string('payment_gateway')->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('transaction_id')->nullable();
            
            // Addresses
            $table->foreignId('shipping_address_id')->constrained('addresses');
            $table->foreignId('billing_address_id')->nullable()->constrained('addresses');
            
            // Shipping
            $table->string('courier')->default('Pakistan Post');
            $table->date('expected_delivery_date')->nullable();
            
            // Status
            $table->enum('status', [
                'pending', 'processing', 'packed', 
                'shipped', 'delivered', 'cancelled', 'returned'
            ])->default('pending');
            
            // Coupon
            $table->string('coupon_code')->nullable();
            
            // Notes
            $table->text('notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            
            $table->index(['order_number']);
            $table->index(['tracking_number']);
            $table->index(['user_id']);
            $table->index(['status']);
            $table->index(['payment_status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
