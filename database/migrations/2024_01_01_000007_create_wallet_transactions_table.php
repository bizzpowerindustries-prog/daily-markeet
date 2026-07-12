<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['credit', 'debit']);
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_after', 12, 2);
            $table->string('source'); // order, withdraw, refund, admin_credit, ad_spend, etc.
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('description');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('completed');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            
            $table->index(['wallet_id']);
            $table->index(['source', 'source_id']);
            $table->index(['status']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
