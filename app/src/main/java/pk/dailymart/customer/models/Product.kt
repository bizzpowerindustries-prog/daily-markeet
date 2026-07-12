package pk.dailymart.customer.models

import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class Product(
    val id: Int,
    val name: String,
    val slug: String,
    val price: Double,
    val sale_price: Double,
    val description: String?,
    val stock: Int,
    val rating: Double,
    val review_count: Int,
    val images: List<ProductImage>,
    val category: Category?,
    val brand: Brand?,
    val seller: Seller?,
    @SerializedName("is_flash_sale")
    val isFlashSale: Boolean = false,
    @SerializedName("flash_sale_ends_at")
    val flashSaleEndsAt: String? = null
) : Serializable

data class ProductDetail(
    val id: Int,
    val name: String,
    val slug: String,
    val price: Double,
    val sale_price: Double,
    val description: String,
    val stock: Int,
    val rating: Double,
    val review_count: Int,
    val images: List<ProductImage>,
    val specs: Map<String, String>?,
    val features: List<String>?,
    val category: Category,
    val brand: Brand?,
    val seller: Seller,
    val variants: List<ProductVariant>?,
    val reviews: List<Review>,
    @SerializedName("related_products")
    val relatedProducts: List<Product>
)

data class ProductImage(
    val id: Int,
    val path: String,
    @SerializedName("is_primary")
    val isPrimary: Boolean
)

data class ProductVariant(
    val id: Int,
    val name: String,
    val value: String,
    val price: Double?,
    val stock: Int?
)

data class Category(
    val id: Int,
    val name: String,
    val slug: String,
    val icon: String?,
    val image: String?,
    val children: List<Category>?
)

data class CategoryDetail(
    val id: Int,
    val name: String,
    val slug: String,
    val description: String?,
    val image: String?,
    val banner: String?,
    val children: List<Category>,
    val products: List<Product>
)

data class Brand(
    val id: Int,
    val name: String,
    val slug: String,
    val logo: String?
)

data class Seller(
    val id: Int,
    @SerializedName("shop_name")
    val shopName: String,
    @SerializedName("shop_slug")
    val shopSlug: String,
    @SerializedName("shop_logo")
    val shopLogo: String?,
    val rating: Double,
    @SerializedName("review_count")
    val reviewCount: Int,
    @SerializedName("is_verified")
    val isVerified: Boolean
)

data class Review(
    val id: Int,
    val user: User,
    val rating: Int,
    val comment: String,
    val images: List<String>?,
    @SerializedName("created_at")
    val createdAt: String,
    @SerializedName("is_approved")
    val isApproved: Boolean
)

data class SearchResult(
    val products: List<Product>,
    val categories: List<Category>,
    val brands: List<Brand>
)
