package pk.dailymart.customer.network

import pk.dailymart.customer.models.*
import retrofit2.http.*
import retrofit2.Response

interface ApiService {
    
    // Auth
    @POST("api/auth/verify")
    suspend fun verifyFirebaseToken(@Body request: FirebaseTokenRequest): Response<ApiResponse<User>>
    
    @POST("api/auth/logout")
    suspend fun logout(): Response<ApiResponse<Any>>
    
    @GET("api/auth/user")
    suspend fun getUser(): Response<ApiResponse<User>>
    
    // Products
    @GET("api/products")
    suspend fun getProducts(
        @Query("page") page: Int = 1,
        @Query("per_page") perPage: Int = 20,
        @Query("category_id") categoryId: Int? = null,
        @Query("brand_id") brandId: Int? = null,
        @Query("min_price") minPrice: Double? = null,
        @Query("max_price") maxPrice: Double? = null,
        @Query("sort") sort: String? = null
    ): Response<ApiResponse<PagedResponse<Product>>>
    
    @GET("api/product/{slug}")
    suspend fun getProduct(@Path("slug") slug: String): Response<ApiResponse<ProductDetail>>
    
    @GET("api/search")
    suspend fun search(@Query("q") query: String): Response<ApiResponse<SearchResult>>
    
    // Categories
    @GET("api/categories")
    suspend fun getCategories(): Response<ApiResponse<List<Category>>>
    
    @GET("api/category/{slug}")
    suspend fun getCategory(@Path("slug") slug: String): Response<ApiResponse<CategoryDetail>>
    
    // Cart
    @GET("api/cart")
    suspend fun getCart(): Response<ApiResponse<List<CartItem>>>
    
    @POST("api/cart/add")
    suspend fun addToCart(@Body request: AddToCartRequest): Response<ApiResponse<CartItem>>
    
    @PUT("api/cart/update/{id}")
    suspend fun updateCartItem(
        @Path("id") id: Int,
        @Body request: UpdateCartItemRequest
    ): Response<ApiResponse<CartItem>>
    
    @DELETE("api/cart/remove/{id}")
    suspend fun removeCartItem(@Path("id") id: Int): Response<ApiResponse<Any>>
    
    @DELETE("api/cart/clear")
    suspend fun clearCart(): Response<ApiResponse<Any>>
    
    // Orders
    @POST("api/order/place")
    suspend fun placeOrder(@Body request: PlaceOrderRequest): Response<ApiResponse<Order>>
    
    @GET("api/orders")
    suspend fun getOrders(
        @Query("page") page: Int = 1
    ): Response<ApiResponse<PagedResponse<Order>>>
    
    @GET("api/order/{id}")
    suspend fun getOrder(@Path("id") id: Int): Response<ApiResponse<OrderDetail>>
    
    @POST("api/order/return/{id}")
    suspend fun requestReturn(
        @Path("id") id: Int,
        @Body request: ReturnRequest
    ): Response<ApiResponse<ReturnResponse>>
    
    @GET("api/order/track/{trackingNumber}")
    suspend fun trackOrder(@Path("trackingNumber") trackingNumber: String): Response<ApiResponse<TrackingResponse>>
    
    // Wallet
    @GET("api/wallet")
    suspend fun getWallet(): Response<ApiResponse<Wallet>>
    
    @POST("api/wallet/add-money")
    suspend fun addMoney(@Body request: AddMoneyRequest): Response<ApiResponse<AddMoneyResponse>>
    
    @GET("api/wallet/transactions")
    suspend fun getWalletTransactions(
        @Query("page") page: Int = 1
    ): Response<ApiResponse<PagedResponse<WalletTransaction>>>
    
    // Addresses
    @GET("api/addresses")
    suspend fun getAddresses(): Response<ApiResponse<List<Address>>>
    
    @POST("api/address")
    suspend fun createAddress(@Body request: CreateAddressRequest): Response<ApiResponse<Address>>
    
    @PUT("api/address/{id}")
    suspend fun updateAddress(
        @Path("id") id: Int,
        @Body request: UpdateAddressRequest
    ): Response<ApiResponse<Address>>
    
    @DELETE("api/address/{id}")
    suspend fun deleteAddress(@Path("id") id: Int): Response<ApiResponse<Any>>
    
    // Wishlist
    @GET("api/wishlist")
    suspend fun getWishlist(): Response<ApiResponse<List<Product>>>
    
    @POST("api/wishlist/add")
    suspend fun addToWishlist(@Body request: AddToWishlistRequest): Response<ApiResponse<Any>>
    
    @DELETE("api/wishlist/remove/{id}")
    suspend fun removeFromWishlist(@Path("id") id: Int): Response<ApiResponse<Any>>
    
    // Notifications
    @GET("api/notifications")
    suspend fun getNotifications(): Response<ApiResponse<List<Notification>>>
    
    @POST("api/notifications/read/{id}")
    suspend fun markNotificationRead(@Path("id") id: Int): Response<ApiResponse<Any>>
    
    @POST("api/notifications/read-all")
    suspend fun markAllNotificationsRead(): Response<ApiResponse<Any>>
    
    // Reviews
    @POST("api/review")
    suspend fun createReview(@Body request: CreateReviewRequest): Response<ApiResponse<Review>>
    
    @GET("api/reviews/product/{productId}")
    suspend fun getProductReviews(
        @Path("productId") productId: Int,
        @Query("page") page: Int = 1
    ): Response<ApiResponse<PagedResponse<Review>>>
}
