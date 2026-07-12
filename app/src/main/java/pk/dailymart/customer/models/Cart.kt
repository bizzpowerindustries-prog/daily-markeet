package pk.dailymart.customer.models

data class CartItem(
    val id: Int,
    val product: Product,
    val quantity: Int,
    val total: Double
)

data class AddToCartRequest(
    @SerializedName("product_id")
    val productId: Int,
    val quantity: Int
)

data class UpdateCartItemRequest(
    val quantity: Int
)
