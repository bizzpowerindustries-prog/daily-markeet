@extends('layouts.app')

@section('title', 'Checkout - DailyMarkeet.pk')

@section('content')
<div x-data="checkoutPage()" x-init="init()" class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Checkout Form -->
        <div class="lg:col-span-2">
            <!-- Address -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="font-semibold mb-4">Shipping Address</h3>
                
                <div x-show="addresses.length === 0">
                    <p class="text-gray-500 text-sm">No saved addresses. Please add one.</p>
                    <button @click="showAddressForm = true" class="mt-2 text-primary-500 hover:underline text-sm">
                        + Add New Address
                    </button>
                </div>
                
                <div x-show="addresses.length > 0" class="space-y-2">
                    <template x-for="address in addresses" :key="address.id">
                        <label class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                            <input type="radio" name="address" :value="address.id" x-model="selectedAddressId" class="mt-1">
                            <div>
                                <p class="font-medium" x-text="address.name"></p>
                                <p class="text-sm text-gray-500" x-text="`${address.address}, ${address.city}, ${address.country}`"></p>
                                <p class="text-sm text-gray-500" x-text="`Phone: ${address.phone}`"></p>
                            </div>
                        </label>
                    </template>
                    <button @click="showAddressForm = true" class="text-sm text-primary-500 hover:underline">+ Add New Address</button>
                </div>
                
                <!-- Add Address Form -->
                <div x-show="showAddressForm" class="mt-4 border-t pt-4">
                    <h4 class="font-medium text-sm mb-3">Add New Address</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <input type="text" x-model="newAddress.name" placeholder="Full Name" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary-500">
                        <input type="text" x-model="newAddress.phone" placeholder="Phone Number" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary-500">
                        <input type="text" x-model="newAddress.address" placeholder="Street Address" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary-500 sm:col-span-2">
                        <input type="text" x-model="newAddress.city" placeholder="City" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary-500">
                        <input type="text" x-model="newAddress.country" placeholder="Country" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary-500">
                        <input type="text" x-model="newAddress.zipCode" placeholder="ZIP Code" class="px-3 py-2 border rounded-lg text-sm focus:outline-none focus:border-primary-500">
                    </div>
                    <div class="flex gap-2 mt-3">
                        <button @click="saveAddress" class="px-4 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition text-sm">
                            Save Address
                        </button>
                        <button @click="showAddressForm = false" class="px-4 py-2 border rounded-lg hover:bg-gray-50 transition text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <h3 class="font-semibold mb-4">Payment Method</h3>
                
                <div class="space-y-2">
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="payment" value="cash_on_delivery" x-model="paymentMethod">
                        <div>
                            <p class="font-medium">Cash on Delivery</p>
                            <p class="text-sm text-gray-500">Pay when you receive your order</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition">
                        <input type="radio" name="payment" value="gateway" x-model="paymentMethod">
                        <div>
                            <p class="font-medium">Online Payment</p>
                            <p class="text-sm text-gray-500">Pay via JazzCash, EasyPaisa, or Card</p>
                        </div>
                    </label>
                    
                    <label class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer hover:bg-gray-50 transition" x-show="walletBalance > 0">
                        <input type="radio" name="payment" value="wallet" x-model="paymentMethod">
                        <div>
                            <p class="font-medium">Wallet</p>
                            <p class="text-sm text-gray-500" x-text="`Balance: Rs. ${walletBalance}`"></p>
                        </div>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-24">
                <h3 class="text-lg font-bold mb-4">Order Summary</h3>
                
                <div class="space-y-2 text-sm max-h-60 overflow-y-auto">
                    <template x-for="item in cartItems" :key="item.id">
                        <div class="flex justify-between">
                            <span x-text="`${item.product.name} × ${item.quantity}`" class="truncate"></span>
                            <span x-text="`Rs. ${item.total}`"></span>
                        </div>
                    </template>
                </div>
                
                <div class="border-t mt-4 pt-4 space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Subtotal</span>
                        <span x-text="`Rs. ${subtotal}`"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Shipping</span>
                        <span x-text="shippingFee > 0 ? `Rs. ${shippingFee}` : 'Free'"></span>
                    </div>
                    <div class="flex justify-between font-bold text-base border-t pt-2">
                        <span>Total</span>
                        <span class="text-primary-500" x-text="`Rs. ${total}`"></span>
                    </div>
                </div>
                
                <button @click="placeOrder" 
                        class="w-full mt-6 bg-primary-500 text-white py-3 rounded-lg hover:bg-primary-600 transition font-semibold"
                        :disabled="!selectedAddressId || isProcessing">
                    <span x-show="!isProcessing">Place Order</span>
                    <span x-show="isProcessing">
                        <i class="fas fa-spinner fa-spin"></i> Processing...
                    </span>
                </button>
                
                <p x-show="error" class="text-red-500 text-sm mt-2" x-text="error"></p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function checkoutPage() {
        return {
            addresses: [],
            selectedAddressId: null,
            paymentMethod: 'cash_on_delivery',
            cartItems: [],
            subtotal: 0,
            shippingFee: 0,
            total: 0,
            walletBalance: 0,
            isProcessing: false,
            error: null,
            showAddressForm: false,
            newAddress: { name: '', phone: '', address: '', city: '', country: '', zipCode: '' },
            
            init() {
                this.loadCart();
                this.loadAddresses();
                this.loadWallet();
            },
            
            loadCart() {
                fetch('/api/cart')
                    .then(res => res.json())
                    .then(data => {
                        this.cartItems = data.data;
                        this.calculateTotals();
                    });
            },
            
            loadAddresses() {
                fetch('/api/addresses')
                    .then(res => res.json())
                    .then(data => {
                        this.addresses = data.data;
                        if (this.addresses.length > 0) {
                            this.selectedAddressId = this.addresses.find(a => a.is_default)?.id || this.addresses[0].id;
                        }
                    });
            },
            
            loadWallet() {
                fetch('/api/wallet')
                    .then(res => res.json())
                    .then(data => {
                        this.walletBalance = data.data?.balance || 0;
                    });
            },
            
            calculateTotals() {
                let subtotal = 0;
                this.cartItems.forEach(item => {
                    subtotal += item.product.salePrice * item.quantity;
                });
                this.subtotal = subtotal;
                this.shippingFee = subtotal > 1000 ? 0 : 200;
                this.total = subtotal + this.shippingFee;
            },
            
            saveAddress() {
                fetch('/api/address', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(this.newAddress)
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.addresses.push(data.data);
                        this.selectedAddressId = data.data.id;
                        this.showAddressForm = false;
                        this.newAddress = { name: '', phone: '', address: '', city: '', country: '', zipCode: '' };
                        toastr.success('Address saved!');
                    }
                });
            },
            
            placeOrder() {
                if (!this.selectedAddressId) {
                    this.error = 'Please select a shipping address';
                    return;
                }
                
                this.isProcessing = true;
                this.error = null;
                
                fetch('/api/order/place', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        shipping_address_id: this.selectedAddressId,
                        payment_method: this.paymentMethod
                    })
                })
                .then(res => res.json())
                .then(data => {
                    this.isProcessing = false;
                    if (data.success) {
                        toastr.success('Order placed successfully!');
                        window.location.href = `/orders/${data.data.id}`;
                    } else {
                        this.error = data.message;
                        toastr.error(data.message);
                    }
                })
                .catch(() => {
                    this.isProcessing = false;
                    this.error = 'Something went wrong';
                });
            }
        }
    }
</script>
@endpush
