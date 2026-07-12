package pk.dailymart.customer.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import pk.dailymart.customer.models.Product
import pk.dailymart.customer.models.ProductDetail
import pk.dailymart.customer.models.PagedResponse
import pk.dailymart.customer.network.ApiService
import pk.dailymart.customer.utils.Resource
import javax.inject.Inject

@HiltViewModel
class ProductViewModel @Inject constructor(
    private val apiService: ApiService
) : ViewModel() {
    
    private val _productsState = MutableStateFlow<Resource<PagedResponse<Product>>>(Resource.Loading())
    val productsState: StateFlow<Resource<PagedResponse<Product>>> = _productsState.asStateFlow()
    
    private val _productDetailState = MutableStateFlow<Resource<ProductDetail>>(Resource.Loading())
    val productDetailState: StateFlow<Resource<ProductDetail>> = _productDetailState.asStateFlow()
    
    private val _searchState = MutableStateFlow<Resource<SearchResult>>(Resource.Loading())
    val searchState: StateFlow<Resource<SearchResult>> = _searchState.asStateFlow()
    
    fun getProducts(
        page: Int = 1,
        categoryId: Int? = null,
        brandId: Int? = null,
        minPrice: Double? = null,
        maxPrice: Double? = null,
        sort: String? = null
    ) {
        viewModelScope.launch {
            _productsState.value = Resource.Loading()
            try {
                val response = apiService.getProducts(page, 20, categoryId, brandId, minPrice, maxPrice, sort)
                if (response.isSuccessful && response.body()?.success == true) {
                    _productsState.value = Resource.Success(response.body()?.data!!)
                } else {
                    _productsState.value = Resource.Error(response.body()?.message ?: "Failed to load products")
                }
            } catch (e: Exception) {
                _productsState.value = Resource.Error(e.message ?: "Network error")
            }
        }
    }
    
    fun getProduct(slug: String) {
        viewModelScope.launch {
            _productDetailState.value = Resource.Loading()
            try {
                val response = apiService.getProduct(slug)
                if (response.isSuccessful && response.body()?.success == true) {
                    _productDetailState.value = Resource.Success(response.body()?.data!!)
                } else {
                    _productDetailState.value = Resource.Error(response.body()?.message ?: "Product not found")
                }
            } catch (e: Exception) {
                _productDetailState.value = Resource.Error(e.message ?: "Network error")
            }
        }
    }
    
    fun search(query: String) {
        viewModelScope.launch {
            _searchState.value = Resource.Loading()
            try {
                val response = apiService.search(query)
                if (response.isSuccessful && response.body()?.success == true) {
                    _searchState.value = Resource.Success(response.body()?.data!!)
                } else {
                    _searchState.value = Resource.Error(response.body()?.message ?: "Search failed")
                }
            } catch (e: Exception) {
                _searchState.value = Resource.Error(e.message ?: "Network error")
            }
        }
    }
}

data class SearchResult(
    val products: List<Product>,
    val categories: List<Category>,
    val brands: List<Brand>
)
