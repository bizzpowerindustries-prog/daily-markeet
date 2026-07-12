@extends('layouts.app')

@section('title', 'Shopping Cart - DailyMarkeet.pk')

@section('content')
<div x-data="cartPage()" x-init="loadCart()" class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Shopping Cart</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Cart Items -->
        <div class="lg:col-span-2">
            <div x-show="items.length === 0" class="text-center py-12">
                <i class="fas fa-shopping-cart text-6xl text-gray-300"></i>
                <h3 class="text-xl font-semibold mt-4">Your cart is empty</h3>
                <p class="text-gray-500 mt-2">Browse our products and add items to your cart</p>
                <a href="{{ route('products.index') }}" class="inline-block mt-4 bg-primary-500 text-white px-6 py-2 rounded-lg hover:bg-primary-600 transition">
                    Start Shopping
                </a>
            </div>
            
            <template x-for="item in items" :key="item.id">
                <div class="bg-white rounded-lg shadow-sm p-4 mb-4 flex items-center gap-4">
                    <img :src="item.product.image" :alt="item.product.name" class="w-20 h-20 object-cover rounded">
                    <div class="flex-1">
                        <a :href="`/product/${item.product.slug}`" class="font-medium hover:text-primary-500 transition" x-text="item.product.name"></a>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-primary-500 font-bold" x-text="`Rs. ${item.product.salePrice}`"></span>
                            <span class="text-gray-400 text-sm line-through" x-text="item.product.price > item.product.salePrice ? `Rs. ${item.product.price}` : ''"></span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click="updateQuantity(item.id, item.quantity - 1)" class="w-8 h-8 border rounded hover:bg-gray-50">-</button>
                        <span class="w-10 text-center" x-text="item.quantity"></span>
                        <button @click="updateQuantity(item.id, item.quantity + 1)" class="w-8 h-8 border rounded hover:bg-gray-50">+</button>
                    </div>
                    <button @click="removeItem(item.id)" class="text-red-500 hover:text-red-600 transition">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </template>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                <h3 class="text-lg font-bold mb-4">Order Summary</h3>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span x-text="`Rs. ${subtotal}`"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Shipping</span>
                        <span x-text="shippingFee > 0 ? `Rs. ${shippingFee}` : 'Free'"></span>
                    </div>
                    <div class="flex justify-between" x-show="discount > 0">
                        <span class="text-green-500">Discount</span>
                        <span class="text-green-500" x-text="`-Rs. ${discount}`"></span>
                    </div>
                    <div class="flex justify-between border-t pt-2 font-bold">
                        <span>Total</span>
                        <span class="text-primary-500" x-text="`Rs. ${total}`"></span>
                    </div>
                </div>
                
                <!-- Coupon -->
                <div class="mt-4">
                    <div class="flex gap-2">
                        <input type="text" x-model="couponCode" placeholder="Enter coupon code" 
                               class="flex-1 px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary-500">
                        <button @click="applyCoupon" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition text-sm">
                            Apply
                        </button>
                    </div>
                    <p x-show="couponMessage" class="text-sm mt-1" :class="couponSuccess ? 'text-green-500' : 'text-red-500'" x-text="couponMessage"></p>
                </div>
                
                <button @click="checkout" 
                        class="w-full mt-6 bg-primary-500 text-white py-3 rounded-lg hover:bg-primary-600 transition font-semibold"
                        :disabled="items.length === 0">
                    Proceed to Checkout
                </button>
                
                <a href="{{ route('products.index') }}" class="block text-center text-sm text-primary-500 mt-2 hover:underline">
                    Continue Shopping
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function cartPage() {
        return {
            items: [],
            subtotal: 0,
            shippingFee: 0,
            discount: 0,
            total: 0,
            couponCode: '',
            couponMessage: '',
            couponSuccess: false,
            
            loadCart() {
                fetch('/api/cart')
                    .then(res => res.json())
                    .then(data => {
                        this.items = data.data;
                        this.calculateTotals();
                    });
            },
            
            updateQuantity(itemId, quantity) {
                if (quantity < 1) {
                    this.removeItem(itemId);
                    return;
                }
                
                fetch(`/api/cart/update/${itemId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ quantity: quantity })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.loadCart();
                        window.dispatchEvent(new Event('cart-updated'));
                    }
                });
            },
            
            removeItem(itemId) {
                if (!confirm('Remove this item from cart?')) return;
                
                fetch(`/api/cart/remove/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.loadCart();
                        window.dispatchEvent(new Event('cart-updated'));
                    }
                });
            },
            
            applyCoupon() {
                if (!this.couponCode) return;
                
                fetch('/api/cart/apply-coupon', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ coupon: this.couponCode })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.couponMessage = 'Coupon applied successfully!';
                        this.couponSuccess = true;
                        this.calculateTotals();
                    } else {
                        this.couponMessage = data.message;
                        this.couponSuccess = false;
                    }
                });
            },
            
            calculateTotals() {
                let subtotal = 0;
                this.items.forEach(item => {
                    subtotal += item.product.salePrice * item.quantity;
                });
                
                this.subtotal = subtotal;
                this.shippingFee = subtotal > 1000 ? 0 : 200;
                this.total = subtotal + this.shippingFee - this.discount;
            },
            
            checkout() {
                if (this.items.length === 0) return;
                window.location.href = '{{ route("checkout.index") }}';
            }
        }
    }
</script>
@endpush
