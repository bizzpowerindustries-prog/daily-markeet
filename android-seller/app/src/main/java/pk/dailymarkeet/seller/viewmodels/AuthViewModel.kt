package pk.dailymarkeet.seller.viewmodels

import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.google.firebase.auth.FirebaseAuth
import dagger.hilt.android.lifecycle.HiltViewModel
import kotlinx.coroutines.flow.MutableStateFlow
import kotlinx.coroutines.flow.StateFlow
import kotlinx.coroutines.launch
import pk.dailymarkeet.seller.models.User
import pk.dailymarkeet.seller.repository.AuthRepository
import pk.dailymarkeet.seller.utils.Resource
import javax.inject.Inject

@HiltViewModel
class AuthViewModel @Inject constructor(
    private val authRepository: AuthRepository,
    private val firebaseAuth: FirebaseAuth
) : ViewModel() {
    
    private val _authState = MutableStateFlow<Resource<User>>(Resource.Loading())
    val authState: StateFlow<Resource<User>> = _authState
    
    fun loginWithEmail(email: String, password: String) {
        viewModelScope.launch {
            _authState.value = Resource.Loading()
            try {
                val result = firebaseAuth.signInWithEmailAndPassword(email, password).await()
                val token = result.user?.getIdToken(true)?.await()?.token
                if (token != null) {
                    val user = authRepository.verifyFirebaseToken(token)
                    // Check if user is a seller
                    if (user.seller == null || user.seller?.status != "approved") {
                        _authState.value = Resource.Error("You are not an approved seller")
                    } else {
                        _authState.value = Resource.Success(user)
                    }
                } else {
                    _authState.value = Resource.Error("Authentication failed")
                }
            } catch (e: Exception) {
                _authState.value = Resource.Error(e.message ?: "Login failed")
            }
        }
    }
    
    fun loginWithGoogle() {
        // Google Sign-In implementation for seller
    }
    
    fun loginWithPhone(phoneNumber: String) {
        // Phone OTP implementation for seller
    }
    
    fun isLoggedIn(): Boolean = authRepository.isLoggedIn()
    
    fun logout() {
        viewModelScope.launch {
            authRepository.logout()
            _authState.value = Resource.Loading()
        }
    }
}
