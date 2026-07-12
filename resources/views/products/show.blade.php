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
                    <h3 class="font
