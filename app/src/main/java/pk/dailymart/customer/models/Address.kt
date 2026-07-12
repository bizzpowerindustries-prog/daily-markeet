package pk.dailymart.customer.models

import com.google.gson.annotations.SerializedName

data class Address(
    val id: Int,
    val name: String,
    val phone: String,
    val address: String,
    val city: String,
    val state: String?,
    val country: String,
    @SerializedName("zip_code")
    val zipCode: String?,
    @SerializedName("is_default")
    val isDefault: Boolean
)

data class CreateAddressRequest(
    val name: String,
    val phone: String,
    val address: String,
    val city: String,
    val state: String?,
    val country: String,
    @SerializedName("zip_code")
    val zipCode: String?,
    @SerializedName("is_default")
    val isDefault: Boolean = false
)

data class UpdateAddressRequest(
    val name: String?,
    val phone: String?,
    val address: String?,
    val city: String?,
    val state: String?,
    val country: String?,
    @SerializedName("zip_code")
    val zipCode: String?,
    @SerializedName("is_default")
    val isDefault: Boolean?
)
