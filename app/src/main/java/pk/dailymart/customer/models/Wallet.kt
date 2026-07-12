package pk.dailymart.customer.models

import com.google.gson.annotations.SerializedName

data class Wallet(
    val id: Int,
    val balance: Double,
    @SerializedName("is_frozen")
    val isFrozen: Boolean
)

data class WalletTransaction(
    val id: Int,
    val type: String, // credit, debit
    val amount: Double,
    @SerializedName("balance_after")
    val balanceAfter: Double,
    val source: String,
    val description: String,
    val status: String,
    @SerializedName("created_at")
    val createdAt: String
)

data class AddMoneyRequest(
    val amount: Double,
    val gateway: String
)

data class AddMoneyResponse(
    @SerializedName("redirect_url")
    val redirectUrl: String,
    val payload: Map<String, String>,
    val gateway: String,
    @SerializedName("transaction_id")
    val transactionId: String
)
