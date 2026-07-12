package pk.dailymart.customer.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.flow.asStateFlow
import kotlinx.coroutines.launch
import pk.dailymart.customer.models.ApiResponse
import pk.dailymart.customer.models.FirebaseTokenRequest
import pk.dailymart.customer.models.User
import pk.dailymart.customer.network.ApiService
import pk.dailymart.customer.repository.AuthRepository
import pk.dailymart.customer.utils.Resource
import javax.inject.Inject

@HiltViewModel
class AuthViewModel @Inject constructor(
    private val authRepository: AuthRepository
) : ViewModel() {
    
    private val _authState = MutableStateFlow<Resource<User>>(Resource.Loading())
    val authState: StateFlow<Resource<User>> = _authState.asStateFlow()
    
    private val _logoutState = MutableStateFlow<Resource<Boolean>>(Resource.Loading())
    val logoutState: StateFlow<Resource<Boolean>> = _logoutState.asStateFlow()
    
    fun verifyFirebaseToken(firebaseToken: String, name: String? = null, email: String? = null) {
        viewModelScope.launch {
            _authState.value = Resource.Loading()
            val result = authRepository.verifyFirebaseToken(firebaseToken, name, email)
            _authState.value = result
        }
    }
    
    fun logout() {
        viewModelScope.launch {
            _logoutState.value = Resource.Loading()
            val result = authRepository.logout()
            _logoutState.value = result
        }
    }
    
    fun getUser(): User? {
        return authRepository.getCurrentUser()
    }
    
    fun isLoggedIn(): Boolean {
        return authRepository.isLoggedIn()
    }
}
