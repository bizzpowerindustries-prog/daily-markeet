<header x-data="headerComponent()" class="bg-white shadow-sm sticky top-0 z-50">
    <!-- Top Bar -->
    <div class="bg-primary-500 text-white text-xs py-1 hidden md:block">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <span>Welcome to DailyMarkeet.pk - Pakistan's No.1 Online Shopping Store</span>
            <div class="flex items-center space-x-4">
                <a href="{{ route('orders.index') }}" class="hover:text-gray-200">
                    <i class="fas fa-box"></i> Track Order
                </a>
                <a href="{{ route('seller.register') }}" class="hover:text-gray-200">
                    <i class="fas fa-store"></i> Sell on DailyMarkeet
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between gap-4">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex-shrink-0">
                <img src="{{ asset('images/logo.png') }}" alt="DailyMarkeet" class="h-10 md:h-12">
            </a>
            
            <!-- Search Bar -->
            <div class="flex-1 max-w-2xl relative" x-data="searchComponent()">
                <form @submit.prevent="search" class="relative">
                    <input 
                        type="text" 
                        x-model="query"
                        @input.debounce="fetchSuggestions"
                        @focus="showSuggestions = true"
                        @click.away="showSuggestions = false"
                        placeholder="Search for products, brands, categories..."
                        class="w-full px-4 py-2.5 pr-12 border-2 border-primary-500 rounded-lg focus:outline-none focus:border-primary-600"
                    >
                    <button type="submit" class="absolute right-1 top-1/2 -translate-y-1/2 bg-primary-500 text-white px-4 py-1.5 rounded-lg hover:bg-primary-600 transition">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                
                <!-- Auto-suggest Dropdown -->
                <div x-show="showSuggestions && suggestions.length > 0" 
                     x-transition:enter.duration.200ms
                     class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 max-h-96 overflow-y-auto z-50">
                    <template x-for="item in suggestions" :key="item.id">
                        <a :href="item.url" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                            <img :src="item.image" alt="" class="w-10 h-10 object-cover rounded">
                            <div>
                                <p class="text-sm font-medium" x-text="item.name"></p>
                                <p class="text-xs text-gray-500" x-text="item.category"></p>
                            </div>
                            <span class="ml-auto text-primary-500 font-semibold" x-text="'Rs.' + item.price"></span>
                        </a>
                    </template>
                </div>
            </div>
            
            <!-- Header Actions -->
            <div class="flex items-center gap-2 md:gap-4 flex-shrink-0">
                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="relative p-2 hover:bg-gray-100 rounded-full transition">
                    <i class="fas fa-heart text-xl text-gray-600"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" x-text="wishlistCount"></span>
                </a>
                
                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="relative p-2 hover:bg-gray-100 rounded-full transition">
                    <i class="fas fa-shopping-cart text-xl text-gray-600"></i>
                    <span class="absolute -top-1 -right-1 bg-primary-500 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center" x-text="cartCount"></span>
                </a>
                
                <!-- User Account -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-lg transition">
                        @auth
                            <img src="{{ auth()->user()->avatar ?? asset('images/default-avatar.png') }}" alt="User" class="w-8 h-8 rounded-full object-cover">
                            <span class="text-sm font-medium hidden md:inline">{{ auth()->user()->name }}</span>
                        @else
                            <i class="fas fa-user-circle text-2xl text-gray-600"></i>
                            <span class="text-sm font-medium hidden md:inline">Account</span>
                        @endauth
                        <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                    </button>
                    
                    <!-- Dropdown -->
                    <div x-show="open" @click.away="open = false" 
                         x-transition:enter.duration.150ms
                         class="absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                        @auth
                            <div class="p-4 border-b border-gray-100">
                                <p class="font-semibold">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                                    <i class="fas fa-user w-5 text-gray-500"></i> My Profile
                                </a>
                                <a href="{{ route('orders.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                                    <i class="fas fa-box w-5 text-gray-500"></i> My Orders
                                </a>
                                <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                                    <i class="fas fa-wallet w-5 text-gray-500"></i> Wallet
                                </a>
                                <a href="{{ route('wishlist.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                                    <i class="fas fa-heart w-5 text-gray-500"></i> Wishlist
                                </a>
                                <a href="{{ route('addresses.index') }}" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                                    <i class="fas fa-map-marker-alt w-5 text-gray-500"></i> Addresses
                                </a>
                            </div>
                            <div class="border-t border-gray-100 py-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition w-full text-left text-red-600">
                                        <i class="fas fa-sign-out-alt w-5"></i> Logout
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="p-4">
                                <a href="{{ route('login') }}" class="block w-full text-center bg-primary-500 text-white py-2 rounded-lg hover:bg-primary-600 transition">
                                    Login
                                </a>
                                <a href="{{ route('register') }}" class="block w-full text-center mt-2 text-primary-500 hover:text-primary-600 transition">
                                    Create Account
                                </a>
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Category Navigation -->
        <div class="mt-3 hidden lg:block">
            <nav class="flex items-center gap-1">
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-2 bg-primary-500 text-white px-4 py-2 rounded-lg hover:bg-primary-600 transition">
                        <i class="fas fa-bars"></i> All Categories
                        <i class="fas fa-chevron-down text-xs"></i>
                    </button>
                    
                    <!-- Categories Dropdown -->
                    <div x-show="open" @click.away="open = false" 
                         class="absolute left-0 mt-1 w-72 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 overflow-y-auto">
                        <div class="py-2">
                            @foreach($categories ?? [] as $category)
                                <a href="{{ route('category.show', $category->slug) }}" 
                                   class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50 transition">
                                    @if($category->icon)
                                        <i class="{{ $category->icon }} w-6 text-primary-500"></i>
                                    @endif
                                    <span>{{ $category->name }}</span>
                                    @if($category->children->isNotEmpty())
                                        <i class="fas fa-chevron-right text-xs ml-auto text-gray-400"></i>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                
                <!-- Nav Links -->
                <a href="{{ route('products.index') }}" class="px-4 py-2 hover:text-primary-500 transition">All Products</a>
                <a href="{{ route('flash-sale.index') }}" class="px-4 py-2 text-red-500 font-semibold hover:text-red-600 transition">
                    <i class="fas fa-bolt"></i> Flash Sale
                </a>
                <a href="{{ route('brands.index') }}" class="px-4 py-2 hover:text-primary-500 transition">Brands</a>
                <a href="{{ route('about') }}" class="px-4 py-2 hover:text-primary-500 transition">About Us</a>
                <a href="{{ route('contact') }}" class="px-4 py-2 hover:text-primary-500 transition">Contact</a>
            </nav>
        </div>
    </div>
</header>

<script>
    function headerComponent() {
        return {
            cartCount: 0,
            wishlistCount: 0,
            init() {
                this.updateCounts();
                window.addEventListener('cart-updated', () => this.updateCounts());
            },
            updateCounts() {
                fetch('/api/cart/count')
                    .then(res => res.json())
                    .then(data => this.cartCount = data.count);
                    
                fetch('/api/wishlist/count')
                    .then(res => res.json())
                    .then(data => this.wishlistCount = data.count);
            }
        }
    }
    
    function searchComponent() {
        return {
            query: '',
            suggestions: [],
            showSuggestions: false,
            search() {
                if (this.query.trim().length > 0) {
                    window.location.href = `/search?q=${encodeURIComponent(this.query)}`;
                }
            },
            fetchSuggestions() {
                if (this.query.trim().length < 2) {
                    this.suggestions = [];
                    return;
                }
                
                fetch(`/api/search?q=${encodeURIComponent(this.query)}`)
                    .then(res => res.json())
                    .then(data => {
                        this.suggestions = data.data.products || [];
                    });
            }
        }
    }
</script>
