package pk.dailymarkeet.seller.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import pk.dailymarkeet.seller.models.DashboardData
import pk.dailymarkeet.seller.network.ApiService
import pk.dailymarkeet.seller.utils.Resource
import javax.inject.Inject

@HiltViewModel
class DashboardViewModel @Inject constructor(
    private val apiService: ApiService
) : ViewModel() {
    
    private val _dashboardState = MutableStateFlow<Resource<DashboardData>>(Resource.Loading())
    val dashboardState: StateFlow<Resource<DashboardData>> = _dashboardState
    
    init {
        loadDashboard()
    }
    
    fun loadDashboard() {
        viewModelScope.launch {
            _dashboardState.value = Resource.Loading()
            try {
                val response = apiService.getSellerDashboard()
                if (response.isSuccessful && response.body()?.success == true) {
                    _dashboardState.value = Resource.Success(response.body()?.data!!)
                } else {
                    _dashboardState.value = Resource.Error(response.body()?.message ?: "Failed to load dashboard")
                }
            } catch (e: Exception) {
                _dashboardState.value = Resource.Error(e.message ?: "Network error")
            }
        }
    }
    
    fun updateOrderStatus(orderId: Int, status: String) {
        viewModelScope.launch {
            try {
                val request = mapOf("status" to status)
                val response = apiService.updateOrderStatus(orderId, request)
                if (response.isSuccessful && response.body()?.success == true) {
                    loadDashboard() // Refresh data
                }
            } catch (e: Exception) {
                // Handle error
            }
        }
    }
}
