@extends('layouts.app')

@section('title', $product->name . ' - DailyMarkeet.pk')

@section('meta_description', $product->meta_description ?? Str::limit($product->description, 160))

@section('content')
<div x-data="productDetail({{ $product->id }})" x-init="init()" class="container mx-auto px-4 py-8">
    <!-- Breadcrumb -->
    <nav class="text-sm mb-6">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="{{ route('home') }}" class="hover:text-primary-500">Home</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li><a href="{{ route('category.show', $product->category->slug) }}" class="hover:text-primary-500">{{ $product->category->name }}</a></li>
            <li><i class="fas fa-chevron-right text-xs"></i></li>
            <li class="text-gray-800">{{ $product->name }}</li>
        </ol>
    </nav>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Product Images -->
        <div>
            <div class="swiper product-gallery rounded-xl overflow-hidden bg-white">
                <div class="swiper-wrapper">
                    @foreach($product->images as $image)
                        <div class="swiper-slide">
                            <img src="{{ asset('storage/' . $image->path) }}" alt="{{ $product->name }}" class="w-full h-96 object-contain">
                        </div>
                    @endforeach
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
            <div class="flex gap-2 mt-2 overflow-x-auto">
                @foreach($product->images as $image)
                    <img src="{{ asset('storage/' . $image->path) }}" 
                         alt="{{ $product->name }}" 
                         class="w-16 h-16 object-cover rounded border-2 hover:border-primary-500 cursor-pointer transition"
                         @click="changeSlide({{ $loop->index }})">
                @endforeach
            </div>
        </div>
        
        <!-- Product Info -->
        <div>
            <h1 class="text-2xl font-bold">{{ $product->name }}</h1>
            
            <!-- Rating -->
            <div class="flex items-center gap-2 mt-2">
                <div class="flex items-center gap-1 text-yellow-400">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= round($product->rating) ? '' : '-o' }}"></i>
                    @endfor
                </div>
                <span class="text-sm text-gray-500">{{ number_format($product->rating, 1) }} ({{ $product->review_count }} reviews)</span>
            </div>
            
            <!-- Price -->
            <div class="mt-4">
                <div class="flex items-center gap-3">
                    <span class="text-3xl font-bold text-primary-500">Rs. {{ number_format($product->sale_price, 2) }}</span>
                    @if($product->price > $product->sale_price)
                        <span class="text-lg text-gray-400 line-through">Rs. {{ number_format($product->price, 2) }}</span>
                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                            {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}% OFF
                        </span>
                    @endif
                </div>
                <p class="text-sm text-green-600 mt-1">
                    <i class="fas fa-check-circle"></i> 
                    {{ $product->stock > 0 ? 'In Stock' : 'Out of Stock' }}
                    ({{ $product->stock }} units available)
                </p>
            </div>
            
            <!-- Product Specs -->
            @if($product->specs)
                <div class="mt-4 bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-sm mb-2">Specifications</h3>
                    <dl class="grid grid-cols-2 gap-1 text-sm">
                        @foreach($product->specs as $key => $value)
                            <dt class="text-gray-500">{{ $key }}</dt>
                            <dd>{{ $value }}</dd>
                        @endforeach
                    </dl>
                </div>
            @endif
            
            <!-- Quantity & Add to Cart -->
            <div class="mt-6 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                <div class="flex items-center border rounded-lg overflow-hidden">
                    <button @click="decrementQuantity" class="px-4 py-2 hover:bg-gray-100 transition">-</button>
                    <span class="px-4 py-2 border-x min-w-[3rem] text-center" x-text="quantity"></span>
                    <button @click="incrementQuantity" class="px-4 py-2 hover:bg-gray-100 transition">+</button>
                </div>
                
                <button @click="addToCart" 
                        class="flex-1 bg-primary-500 text-white px-6 py-3 rounded-lg hover:bg-primary-600 transition font-semibold flex items-center justify-center gap-2">
                    <i class="fas fa-shopping-cart"></i> Add to Cart
                </button>
                
                <button @click="addToWishlist" 
                        class="p-3 border rounded-lg hover:bg-gray-50 transition">
                    <i class="fas fa-heart text-2xl" :class="isWishlisted ? 'text-red-500' : 'text-gray-400'"></i>
                </button>
            </div>
            
            <!-- Buy Now -->
            <button @click="buyNow" 
                    class="w-full mt-2 bg-green-500 text-white px-6 py-3 rounded-lg hover:bg-green-600 transition font-semibold">
                Buy Now
            </button>
            
            <!-- Seller Info -->
            <div class="mt-6 border rounded-lg p-4">
                <h3 class="font-semibold mb-2">Seller Information</h3>
                <div class="flex items-center gap-4">
                    <img src="{{ asset('storage/' . $product->seller->shop_logo ?? 'default-shop.png') }}" 
                         alt="{{ $product->seller->shop_name }}" class="w-12 h-12 rounded-full object-cover">
                    <div>
                        <p class="font-medium">{{ $product->seller->shop_name }}</p>
                        <p class="text-sm text-gray-500">
                            <i class="fas fa-star text-yellow-400"></i> {{ number_format($product->seller->rating, 1) }}
                            ({{ $product->seller->review_count }} ratings)
                        </p>
                        <a href="{{ route('seller.show', $product->seller->shop_slug) }}" class="text-primary-500 text-sm hover:underline">
                            View Shop
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product Description -->
    <div class="mt-12">
        <h2 class="text-xl font-bold mb-4">Product Description</h2>
        <div class="bg-white rounded-lg shadow-sm p-6 prose max-w-none">
            {!! $product->description !!}
        </div>
    </div>
    
    <!-- Reviews -->
    <div class="mt-12">
        <h2 class="text-xl font-bold mb-4">Customer Reviews</h2>
        
        <!-- Review Summary -->
        <div class="flex items-start gap-8 bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="text-center">
                <span class="text-4xl font-bold text-primary-500">{{ number_format($product->rating, 1) }}</span>
                <div class="flex items-center justify-center gap-1 text-yellow-400 mt-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= round($product->rating) ? '' : '-o' }}"></i>
                    @endfor
                </div>
                <span class="text-sm text-gray-500">{{ $product->review_count }} reviews</span>
            </div>
            
            <div class="flex-1">
                @foreach([5,4,3,2,1] as $star)
                    <div class="flex items-center gap-2">
                        <span class="text-sm w-8">{{ $star }}★</span>
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400 rounded-full" 
                                 style="width: {{ $product->review_count > 0 ? (($product->reviews->where('rating', $star)->count() / $product->review_count) * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-sm text-gray-500 w-12">{{ $product->reviews->where('rating', $star)->count() }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Review List -->
        <div class="space-y-4">
            @foreach($product->reviews->where('is_approved', true) as $review)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex items-start gap-4">
                        <img src="{{ asset('images/default-avatar.png') }}" alt="{{ $review->user->name }}" class="w-10 h-10 rounded-full object-cover">
                        <div>
                            <p class="font-medium">{{ $review->user->name }}</p>
                            <div class="flex items-center gap-2">
                                <div class="flex items-center gap-1 text-yellow-400 text-sm">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star{{ $i <= $review->rating ? '' : '-o' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-400">{{ $review->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="mt-2 text-gray-700">{{ $review->comment }}</p>
                            @if($review->images)
                                <div class="flex gap-2 mt-2">
                                    @foreach($review->images as $image)
                                        <img src="{{ asset('storage/' . $image) }}" alt="Review Image" class="w-16 h-16 object-cover rounded">
                                    @endforeach
                                </div>
                            @endif
                            @if($review->reply)
                                <div class="mt-3 bg-gray-50 rounded p-3">
                                    <p class="text-sm font-medium text-primary-500">Seller Response:</p>
                                    <p class="text-sm text-gray-600">{{ $review->reply }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Related Products -->
    <div class="mt-12">
        <h2 class="text-xl font-bold mb-4">Related Products</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($relatedProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    function productDetail(productId) {
        return {
            productId: productId,
            quantity: 1,
            isWishlisted: false,
            
            init() {
                this.checkWishlist();
                this.initGallery();
            },
            
            initGallery() {
                new Swiper('.product-gallery', {
                    loop: true,
                    pagination: { el: '.swiper-pagination' },
                    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }
                });
            },
            
            changeSlide(index) {
                const swiper = document.querySelector('.product-gallery').swiper;
                if (swiper) swiper.slideTo(index);
            },
            
            incrementQuantity() {
                if (this.quantity < 10) this.quantity++;
            },
            
            decrementQuantity() {
                if (this.quantity > 1) this.quantity--;
            },
            
            addToCart() {
                fetch('/api/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        product_id: this.productId,
                        quantity: this.quantity
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Product added to cart!');
                        window.dispatchEvent(new Event('cart-updated'));
                    } else {
                        toastr.error(data.message);
                    }
                });
            },
            
            addToWishlist() {
                fetch('/api/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ product_id: this.productId })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.isWishlisted = data.isWishlisted;
                        toastr.success(data.isWishlisted ? 'Added to wishlist!' : 'Removed from wishlist!');
                    }
                });
            },
            
            checkWishlist() {
                fetch('/api/wishlist/check/' + this.productId)
                    .then(res => res.json())
                    .then(data => {
                        this.isWishlisted = data.isWishlisted;
                    });
            },
            
            buyNow() {
                this.addToCart();
                window.location.href = '{{ route("checkout.index") }}';
            }
        }
    }
</script>
@endpush
