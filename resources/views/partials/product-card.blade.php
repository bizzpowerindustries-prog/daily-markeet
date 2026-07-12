<div class="bg-white rounded-lg shadow-sm hover:shadow-lg transition overflow-hidden group">
    <a :href="`/product/${product.slug}`">
        <div class="relative">
            <img :src="product.image" :alt="product.name" class="w-full h-48 object-cover group-hover:scale-105 transition duration-300">
            <div x-show="product.discount > 0" class="absolute top-2 left-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
                -<span x-text="product.discount"></span>%
            </div>
            <button @click.prevent="addToWishlist(product.id)" class="absolute top-2 right-2 bg-white rounded-full p-2 shadow-md hover:bg-gray-100 transition">
                <i class="fas fa-heart text-gray-400 hover:text-red-500 transition"></i>
            </button>
        </div>
        <div class="p-3">
            <h3 class="font-medium text-sm line-clamp-2" x-text="product.name"></h3>
            <div class="flex items-center gap-2 mt-1">
                <span class="text-primary-500 font-bold" x-text="`Rs. ${product.salePrice}`"></span>
                <span x-show="product.price > product.salePrice" class="text-gray-400 text-sm line-through" x-text="`Rs. ${product.price}`"></span>
            </div>
            <div class="flex items-center gap-1 mt-1 text-sm text-gray-500">
                <i class="fas fa-star text-yellow-400"></i>
                <span x-text="product.rating"></span>
                <span class="text-xs">(<span x-text="product.reviewCount"></span>)</span>
            </div>
        </div>
    </a>
</div>
