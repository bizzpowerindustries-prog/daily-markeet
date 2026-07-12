<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained('sellers')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            $table->decimal('price', 12, 2);
            $table->decimal('sale_price', 12, 2);
            $table->integer('stock')->default(0);
            
            $table->json('specs')->nullable();
            $table->json('features')->nullable();
            
            // Weight & dimensions for shipping
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();
            
            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            
            // Stats
            $table->integer('views')->default(0);
            $table->integer('sales_count')->default(0);
            $table->decimal('rating', 2, 1)->default(0);
            $table->integer('review_count')->default(0);
            
            // Status
            $table->enum('status', ['pending', 'active', 'inactive', 'deleted'])->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->timestamp('approved_at')->nullable();
            
            // Flash sale
            $table->boolean('is_flash_sale')->default(false);
            $table->timestamp('flash_sale_ends_at')->nullable();
            
            $table->timestamps();
            
            $table->index(['slug']);
            $table->index(['seller_id']);
            $table->index(['category_id']);
            $table->index(['brand_id']);
            $table->index(['status']);
            $table->index(['is_approved']);
            $table->index(['sale_price']);
            $table->index(['created_at']);
            $table->fullText(['name', 'description']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
