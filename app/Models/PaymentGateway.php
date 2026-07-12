<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentGateway extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'credentials',
        'fee_percent',
        'fee_fixed',
        'status',
        'priority',
        'is_test_mode'
    ];

    protected $casts = [
        'credentials' => 'array',
        'fee_percent' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
        'status' => 'boolean',
        'is_test_mode' => 'boolean'
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
