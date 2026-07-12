package pk.dailymarkeet.seller.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import pk.dailymarkeet.seller.models.SellerProduct
import pk.dailymarkeet.seller.network.ApiService
import pk.dailymarkeet.seller.utils.Resource
import javax.inject.Inject

@HiltViewModel
class ProductManagementViewModel @Inject constructor(
    private val apiService: ApiService
) : ViewModel() {
    
    private val _productsState = MutableStateFlow<Resource<List<SellerProduct>>>(Resource.Loading())
    val productsState: StateFlow<Resource<List<SellerProduct>>> = _productsState
    
    init {
        loadProducts()
    }
    
    fun loadProducts() {
        viewModelScope.launch {
            _productsState.value = Resource.Loading()
            try {
                val response = apiService.getSellerProducts()
                if (response.isSuccessful && response.body()?.success == true) {
                    _productsState.value = Resource.Success(response.body()?.data ?: emptyList())
                } else {
                    _productsState.value = Resource.Error(response.body()?.message ?: "Failed to load products")
                }
            } catch (e: Exception) {
                _productsState.value = Resource.Error(e.message ?: "Network error")
            }
        }
    }
    
    fun toggleProductStatus(productId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.toggleProductStatus(productId)
                if (response.isSuccessful && response.body()?.success == true) {
                    loadProducts() // Refresh list
                }
            } catch (e: Exception) {
                // Handle error
            }
        }
    }
    
    fun deleteProduct(productId: Int) {
        viewModelScope.launch {
            try {
                val response = apiService.deleteProduct(productId)
                if (response.isSuccessful && response.body()?.success == true) {
                    loadProducts() // Refresh list
                }
            } catch (e: Exception) {
                // Handle error
            }
        }
    }
}
