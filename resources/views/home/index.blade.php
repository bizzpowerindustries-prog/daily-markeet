@extends('layouts.app')

@section('title', 'DailyMarkeet.pk - Pakistan\'s Best Online Shopping Store')

@section('content')
<div x-data="homepage()" x-init="init()">
    <!-- Hero Slider -->
    <section class="container mx-auto px-4 mt-4">
        <div class="swiper hero-slider rounded-xl overflow-hidden shadow-lg">
            <div class="swiper-wrapper">
                <template x-for="slide in slides" :key="slide.id">
                    <div class="swiper-slide">
                        <img :src="slide.image" :alt="slide.title" class="w-full h-64 md:h-96 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center">
                            <div class="px-8 md:px-16 max-w-xl">
                                <h2 class="text-white text-2xl md:text-4xl font-bold" x-text="slide.title"></h2>
                                <p class="text-white/90 text-sm md:text-base mt-2" x-text="slide.subtitle"></p>
                                <a :href="slide.link" class="inline-block mt-4 bg-primary-500 text-white px-6 py-2 rounded-lg hover:bg-primary-600 transition">
                                    Shop Now
                                </a>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-next"></div>
            <div class="swiper-button-prev"></div>
        </div>
    </section>
    
    <!-- Categories Grid -->
    <section class="container mx-auto px-4 mt-8">
        <h2 class="text-xl font-bold mb-4">Shop by Category</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <template x-for="category in categories" :key="category.id">
                <a :href="`/category/${category.slug}`" 
                   class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-4 text-center group">
                    <div class="w-16 h-16 mx-auto bg-primary-50 rounded-full flex items-center justify-center group-hover:bg-primary-100 transition">
                        <i :class="category.icon" class="text-2xl text-primary-500"></i>
                    </div>
                    <p class="text-sm font-medium mt-2" x-text="category.name"></p>
                </a>
            </template>
        </div>
    </section>
    
    <!-- Flash Sale -->
    <section class="container mx-auto px-4 mt-8" x-show="flashSale">
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-xl p-4 md:p-6 text-white">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-xl font-bold flex items-center gap-2">
                        <i class="fas fa-bolt"></i> Flash Sale
                    </h2>
                    <p class="text-white/80 text-sm">Hurry up! Limited time offer</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="bg-white/20 rounded-lg px-4 py-2 text-center">
                        <span class="text-xs font-semibold">Ends in</span>
                        <div class="flex items-center gap-2 text-xl font-bold">
                            <span x-text="flashSale.timeLeft.days"></span>d
                            <span x-text="flashSale.timeLeft.hours"></span>h
                            <span x-text="flashSale.timeLeft.minutes"></span>m
                            <span x-text="flashSale.timeLeft.seconds"></span>s
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mt-4">
                <div class="swiper flash-sale-slider">
                    <div class="swiper-wrapper">
                        <template x-for="product in flashSale.products" :key="product.id">
                            <div class="swiper-slide">
                                @include('partials.product-card', ['product' => '{{ product }}'])
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Sponsored Products -->
    <section class="container mx-auto px-4 mt-8">
        <h2 class="text-xl font-bold mb-4">Sponsored Products</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <template x-for="product in sponsoredProducts" :key="product.id">
                @include('partials.product-card', ['product' => '{{ product }}'])
            </template>
        </div>
    </section>
    
    <!-- Just For You -->
    <section class="container mx-auto px-4 mt-8">
        <h2 class="text-xl font-bold mb-4">Just For You</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <template x-for="product in justForYou" :key="product.id">
                @include('partials.product-card', ['product' => '{{ product }}'])
            </template>
        </div>
    </section>
    
    <!-- Brands -->
    <section class="container mx-auto px-4 mt-8">
        <h2 class="text-xl font-bold mb-4">Top Brands</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            <template x-for="brand in brands" :key="brand.id">
                <a :href="`/brand/${brand.slug}`" class="bg-white rounded-lg shadow-sm hover:shadow-md transition p-4 text-center">
                    <img :src="brand.logo" :alt="brand.name" class="h-12 mx-auto object-contain">
                </a>
            </template>
        </div>
    </section>
</div>

@push('scripts')
<script>
    function homepage() {
        return {
            slides: [],
            categories: [],
            flashSale: null,
            sponsoredProducts: [],
            justForYou: [],
            brands: [],
            
            init() {
                this.loadData();
                this.initSliders();
            },
            
            loadData() {
                fetch('/api/homepage')
                    .then(res => res.json())
                    .then(data => {
                        this.slides = data.data.slides;
                        this.categories = data.data.categories;
                        this.flashSale = data.data.flashSale;
                        this.sponsoredProducts = data.data.sponsoredProducts;
                        this.justForYou = data.data.justForYou;
                        this.brands = data.data.brands;
                        
                        // Initialize sliders after data is loaded
                        setTimeout(() => this.initSliders(), 100);
                    });
            },
            
            initSliders() {
                // Hero Slider
                new Swiper('.hero-slider', {
                    loop: true,
                    autoplay: { delay: 5000 },
                    pagination: { el: '.swiper-pagination' },
                    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                    effect: 'fade'
                });
                
                // Flash Sale Slider
                if (this.flashSale) {
                    new Swiper('.flash-sale-slider', {
                        slidesPerView: 2,
                        spaceBetween: 16,
                        breakpoints: {
                            640: { slidesPerView: 3 },
                            768: { slidesPerView: 4 },
                            1024: { slidesPerView: 5 }
                        }
                    });
                }
            }
        }
    }
</script>
@endpush
