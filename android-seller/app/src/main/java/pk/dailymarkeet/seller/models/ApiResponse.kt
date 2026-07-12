package pk.dailymarkeet.seller.models

data class ApiResponse<T>(
    val success: Boolean,
    val message: String? = null,
    val data: T? = null,
    val errors: Map<String, List<String>>? = null
)

data class PagedResponse<T>(
    val data: List<T>,
    val current_page: Int,
    val last_page: Int,
    val per_page: Int,
    val total: Int
)
