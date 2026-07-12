<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('shop_name');
            $table->string('shop_slug')->unique();
            $table->text('shop_description')->nullable();
            $table->string('shop_logo')->nullable();
            $table->string('shop_banner')->nullable();
            
            // Business details
            $table->string('cnic')->nullable();
            $table->string('ntn')->nullable();
            $table->string('business_registration')->nullable();
            
            // Bank details
            $table->string('bank_name')->nullable();
            $table->string('bank_account_title')->nullable();
            $table->string('iban')->nullable();
            
            // Address
            $table->string('shop_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('zip_code')->nullable();
            
            // Commission
            $table->decimal('commission_override', 5, 2)->nullable();
            
            // Status
            $table->enum('status', ['pending', 'approved', 'suspended', 'banned'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('suspended_at')->nullable();
            
            // Documents
            $table->json('documents')->nullable();
            
            $table->timestamps();
            
            $table->index(['user_id']);
            $table->index(['shop_slug']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sellers');
    }
};
