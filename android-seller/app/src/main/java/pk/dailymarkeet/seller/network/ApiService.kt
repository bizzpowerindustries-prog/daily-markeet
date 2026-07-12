package pk.dailymarkeet.seller.network

import pk.dailymarkeet.seller.models.*
import retrofit2.Response
import retrofit2.http.*

interface ApiService {
    
    // Auth
    @POST("api/auth/verify")
    suspend fun verifyFirebaseToken(@Body request: Map<String, String>): Response<ApiResponse<User>>
    
    @POST("api/auth/logout")
    suspend fun logout(): Response<ApiResponse<Any>>
    
    // Dashboard
    @GET("api/seller/dashboard")
    suspend fun getSellerDashboard(): Response<ApiResponse<DashboardData>>
    
    // Products
    @GET("api/seller/products")
    suspend fun getSellerProducts(): Response<ApiResponse<List<SellerProduct>>>
    
    @POST("api/seller/product")
    suspend fun createProduct(@Body product: CreateProductRequest): Response<ApiResponse<SellerProduct>>
    
    @PUT("api/seller/product/{id}")
    suspend fun updateProduct(
        @Path("id") id: Int,
        @Body product: UpdateProductRequest
    ): Response<ApiResponse<SellerProduct>>
    
    @DELETE("api/seller/product/{id}")
    suspend fun deleteProduct(@Path("id") id: Int): Response<ApiResponse<Any>>
    
    @PUT("api/seller/product/{id}/toggle-status")
    suspend fun toggleProductStatus(@Path("id") id: Int): Response<ApiResponse<Any>>
    
    // Orders
    @GET("api/seller/orders")
    suspend fun getSellerOrders(): Response<ApiResponse<List<Order>>>
    
    @PUT("api/seller/order/{id}/status")
    suspend fun updateOrderStatus(
        @Path("id") id: Int,
        @Body request: Map<String, String>
    ): Response<ApiResponse<Order>>
    
    @POST("api/seller/order/{id}/tracking")
    suspend fun addTrackingEvent(
        @Path("id") id: Int,
        @Body request: Map<String, String>
    ): Response<ApiResponse<Any>>
    
    // Wallet
    @GET("api/seller/wallet")
    suspend fun getSellerWallet(): Response<ApiResponse<SellerWallet>>
    
    @POST("api/seller/wallet/withdraw")
    suspend fun requestWithdrawal(@Body request: WithdrawRequest): Response<ApiResponse<Any>>
    
    @GET("api/seller/wallet/transactions")
    suspend fun getWalletTransactions(): Response<ApiResponse<List<SellerWalletTransaction>>>
    
    // Profile
    @GET("api/seller/profile")
    suspend fun getSellerProfile(): Response<ApiResponse<SellerProfile>>
    
    @PUT("api/seller/profile")
    suspend fun updateSellerProfile(@Body profile: SellerProfile): Response<ApiResponse<SellerProfile>>
}
