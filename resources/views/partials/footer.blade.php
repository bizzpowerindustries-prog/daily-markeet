<footer class="bg-gray-900 text-gray-300 mt-12">
    <!-- Newsletter -->
    <div class="border-b border-gray-800">
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-2xl mx-auto text-center">
                <h3 class="text-xl font-semibold text-white mb-2">Subscribe to Our Newsletter</h3>
                <p class="text-gray-400 mb-4">Get the latest updates on new products and upcoming sales</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="flex flex-col sm:flex-row gap-2">
                    @csrf
                    <input type="email" name="email" placeholder="Enter your email" 
                           class="flex-1 px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:border-primary-500 text-white">
                    <button type="submit" class="px-6 py-2 bg-primary-500 text-white rounded-lg hover:bg-primary-600 transition whitespace-nowrap">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Main Footer -->
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- About -->
            <div>
                <h4 class="text-white font-semibold mb-4">About DailyMarkeet</h4>
                <p class="text-sm leading-relaxed">DailyMarkeet.pk is Pakistan's leading online shopping platform offering a wide range of products at competitive prices.</p>
                <div class="flex gap-4 mt-4">
                    <a href="#" class="hover:text-white transition"><i class="fab fa-facebook-f text-xl"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-twitter text-xl"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-instagram text-xl"></i></a>
                    <a href="#" class="hover:text-white transition"><i class="fab fa-youtube text-xl"></i></a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div>
                <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact Us</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white transition">Terms & Conditions</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white transition">Privacy Policy</a></li>
                    <li><a href="{{ route('returns') }}" class="hover:text-white transition">Return Policy</a></li>
                </ul>
            </div>
            
            <!-- Customer Service -->
            <div>
                <h4 class="text-white font-semibold mb-4">Customer Service</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('faq') }}" class="hover:text-white transition">FAQ</a></li>
                    <li><a href="{{ route('track-order') }}" class="hover:text-white transition">Track Order</a></li>
                    <li><a href="{{ route('returns') }}" class="hover:text-white transition">Returns & Refunds</a></li>
                    <li><a href="{{ route('shipping') }}" class="hover:text-white transition">Shipping Info</a></li>
                    <li><a href="{{ route('seller.register') }}" class="hover:text-white transition">Sell on DailyMarkeet</a></li>
                </ul>
            </div>
            
            <!-- Contact -->
            <div>
                <h4 class="text-white font-semibold mb-4">Contact Us</h4>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start gap-3">
                        <i class="fas fa-phone text-primary-500 mt-1"></i>
                        <span>+92 300 1234567</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-envelope text-primary-500 mt-1"></i>
                        <span>info@dailymarkeet.pk</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <i class="fas fa-map-marker-alt text-primary-500 mt-1"></i>
                        <span>123, Main Boulevard,<br>Islamabad, Pakistan</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <!-- Payment Methods -->
        <div class="border-t border-gray-800 mt-8 pt-8">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-xs">&copy; {{ date('Y') }} DailyMarkeet.pk. All rights reserved.</p>
                <div class="flex items-center gap-3 text-2xl">
                    <i class="fab fa-cc-visa text-gray-400"></i>
                    <i class="fab fa-cc-mastercard text-gray-400"></i>
                    <i class="fab fa-cc-amex text-gray-400"></i>
                    <i class="fas fa-money-bill-wave text-gray-400"></i>
                    <span class="text-sm font-semibold text-gray-400">JazzCash</span>
                    <span class="text-sm font-semibold text-gray-400">EasyPaisa</span>
                </div>
            </div>
        </div>
    </div>
</footer>
