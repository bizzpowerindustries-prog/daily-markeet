<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total',
        'subtotal',
        'tax',
        'shipping_fee',
        'discount',
        'coupon_code',
        'payment_method',
        'payment_gateway',
        'payment_status',
        'shipping_address_id',
        'billing_address_id',
        'tracking_number',
        'courier',
        'status',
        'expected_delivery_date',
        'notes'
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'discount' => 'decimal:2',
        'expected_delivery_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingAddress()
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function billingAddress()
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    public function trackingEvents()
    {
        return $this->hasMany(OrderTrackingEvent::class);
    }

    public function return()
    {
        return $this->hasOne(ReturnRequest::class);
    }

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Helper methods
    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'packed' => 'Packed',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled',
            'returned' => 'Returned'
        ][$this->status] ?? $this->status;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'processing']);
    }

    public function canBeReturned(): bool
    {
        return $this->status === 'delivered' && 
               !$this->return && 
               $this->created_at->diffInDays(now()) <= 7;
    }

    public function generateTrackingNumber()
    {
        $this->tracking_number = 'DM-' . strtoupper(uniqid());
        $this->save();
        return $this->tracking_number;
    }
}
