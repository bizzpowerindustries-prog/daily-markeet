package pk.dailymart.customer.models

import com.google.gson.annotations.SerializedName
import java.io.Serializable

data class Order(
    val id: Int,
    @SerializedName("order_number")
    val orderNumber: String,
    @SerializedName("tracking_number")
    val trackingNumber: String?,
    val subtotal: Double,
    @SerializedName("shipping_fee")
    val shippingFee: Double,
    val tax: Double,
    val discount: Double,
    val total: Double,
    @SerializedName("payment_method")
    val paymentMethod: String,
    @SerializedName("payment_status")
    val paymentStatus: String,
    val status: String,
    @SerializedName("created_at")
    val createdAt: String,
    @SerializedName("expected_delivery_date")
    val expectedDeliveryDate: String?,
    @SerializedName("courier")
    val courier: String
)

data class OrderDetail(
    val id: Int,
    @SerializedName("order_number")
    val orderNumber: String,
    @SerializedName("tracking_number")
    val trackingNumber: String?,
    val subtotal: Double,
    @SerializedName("shipping_fee")
    val shippingFee: Double,
    val tax: Double,
    val discount: Double,
    val total: Double,
    @SerializedName("payment_method")
    val paymentMethod: String,
    @SerializedName("payment_status")
    val paymentStatus: String,
    val status: String,
    @SerializedName("created_at")
    val createdAt: String,
    @SerializedName("expected_delivery_date")
    val expectedDeliveryDate: String?,
    @SerializedName("courier")
    val courier: String,
    val items: List<OrderItem>,
    @SerializedName("tracking_events")
    val trackingEvents: List<TrackingEvent>,
    @SerializedName("shipping_address")
    val shippingAddress: Address,
    val return: ReturnResponse?
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

data class TrackingResponse(
    @SerializedName("tracking_number")
    val trackingNumber: String,
    @SerializedName("current_status")
    val currentStatus: String,
    val courier: String,
    @SerializedName("expected_delivery")
    val expectedDelivery: String?,
    val events: List<TrackingEvent>
)

data class PlaceOrderRequest(
    @SerializedName("shipping_address_id")
    val shippingAddressId: Int,
    @SerializedName("payment_method")
    val paymentMethod: String,
    val gateway: String? = null,
    @SerializedName("coupon_code")
    val couponCode: String? = null,
    val notes: String? = null
)

data class ReturnRequest(
    val reason: String,
    val items: List<ReturnItem>,
    val images: List<String>? = null
)

data class ReturnItem(
    @SerializedName("order_item_id")
    val orderItemId: Int,
    val quantity: Int
)

data class ReturnResponse(
    val id: Int,
    val status: String,
    @SerializedName("return_amount")
    val returnAmount: Double,
    val reason: String,
    val items: List<ReturnItem>
)
