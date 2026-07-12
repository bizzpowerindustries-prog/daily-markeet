package pk.dailymarkeet.seller.models

import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class DashboardData(
    val stats: DashboardStats,
    @SerializedName("recent_orders")
    val recentOrders: List<Order>,
    @SerializedName("wallet_balance")
    val walletBalance: Double,
    @SerializedName("today_earnings")
    val todayEarnings: Double,
    @SerializedName("top_products")
    val topProducts: List<ProductPerformance>
)

data class DashboardStats(
    @SerializedName("total_orders")
    val totalOrders: Int,
    @SerializedName("pending_orders")
    val pendingOrders: Int,
    @SerializedName("shipped_orders")
    val shippedOrders: Int,
    @SerializedName("total_products")
    val totalProducts: Int,
    @SerializedName("total_earnings")
    val totalEarnings: Double,
    @SerializedName("commission_rate")
    val commissionRate: Double
)

data class ProductPerformance(
    val id: Int,
    val name: String,
    @SerializedName("sales_count")
    val salesCount: Int,
    @SerializedName("view_count")
    val viewCount: Int,
    val revenue: Double
)

data class SellerProduct(
    val id: Int,
    val name: String,
    val slug: String,
    val price: Double,
    @SerializedName("sale_price")
    val salePrice: Double,
    val stock: Int,
    val status: String,
    @SerializedName("is_approved")
    val isApproved: Boolean,
    @SerializedName("images")
    val images: List<ProductImage>,
    @SerializedName("category")
    val category: Category?,
    @SerializedName("brand")
    val brand: Brand?,
    @SerializedName("created_at")
    val createdAt: String
)

data class ProductImage(
    val id: Int,
    val path: String,
    @SerializedName("is_primary")
    val isPrimary: Boolean
)

data class Category(
    val id: Int,
    val name: String,
    val slug: String
)

data class Brand(
    val id: Int,
    val name: String,
    val slug: String
)

data class CreateProductRequest(
    val name: String,
    val description: String,
    @SerializedName("category_id")
    val categoryId: Int,
    @SerializedName("brand_id")
    val brandId: Int? = null,
    val price: Double,
    @SerializedName("sale_price")
    val salePrice: Double,
    val stock: Int,
    val specs: Map<String, String>? = null,
    @SerializedName("images")
    val images: List<String>? = null
)

data class UpdateProductRequest(
    val name: String? = null,
    val description: String? = null,
    @SerializedName("category_id")
    val categoryId: Int? = null,
    @SerializedName("brand_id")
    val brandId: Int? = null,
    val price: Double? = null,
    @SerializedName("sale_price")
    val salePrice: Double? = null,
    val stock: Int? = null,
    val status: String? = null
)

data class SellerOrder(
    val id: Int,
    @SerializedName("order_number")
    val orderNumber: String,
    val total: Double,
    val status: String,
    @SerializedName("customer_name")
    val customerName: String,
    @SerializedName("customer_phone")
    val customerPhone: String,
    @SerializedName("shipping_address")
    val shippingAddress: String,
    @SerializedName("created_at")
    val createdAt: String,
    val items: List<OrderItem>,
    @SerializedName("tracking_events")
    val trackingEvents: List<TrackingEvent>
)

data class OrderItem(
    val id: Int,
    val product: Product,
    val quantity: Int,
    val price: Double,
    val total: Double
)

data class TrackingEvent(
    val status: String,
    val description: String,
    val location: String?,
    @SerializedName("event_time")
    val eventTime: String
)

data class UpdateOrderStatusRequest(
    val status: String,
    @SerializedName("tracking_id")
    val trackingId: String? = null,
    val location: String? = null,
    val description: String? = null
)

data class WithdrawRequest(
    val amount: Double,
    @SerializedName("bank_name")
    val bankName: String,
    @SerializedName("account_title")
    val accountTitle: String,
    @SerializedName("account_number")
    val accountNumber: String,
    val iban: String
)

data class SellerWallet(
    val id: Int,
    val balance: Double,
    @SerializedName("is_frozen")
    val isFrozen: Boolean
)

data class SellerWalletTransaction(
    val id: Int,
    val type: String,
    val amount: Double,
    @SerializedName("balance_after")
    val balanceAfter: Double,
    val source: String,
    val description: String,
    val status: String,
    @SerializedName("created_at")
    val createdAt: String
)

data class SellerProfile(
    @SerializedName("shop_name")
    val shopName: String,
    @SerializedName("shop_slug")
    val shopSlug: String,
    @SerializedName("shop_description")
    val shopDescription: String?,
    @SerializedName("shop_logo")
    val shopLogo: String?,
    @SerializedName("shop_banner")
    val shopBanner: String?,
    val cnic: String?,
    val ntn: String?,
    @SerializedName("bank_name")
    val bankName: String?,
    val iban: String?,
    @SerializedName("shop_address")
    val shopAddress: String?,
    val city: String?,
    val status: String,
    @SerializedName("commission_override")
    val commissionOverride: Double?
)
