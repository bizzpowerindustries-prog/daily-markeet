<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->morphs('owner');
            $table->decimal('balance', 12, 2)->default(0);
            $table->boolean('is_frozen')->default(false);
            $table->timestamp('frozen_at')->nullable();
            $table->timestamps();
            
            $table->index(['owner_id', 'owner_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
