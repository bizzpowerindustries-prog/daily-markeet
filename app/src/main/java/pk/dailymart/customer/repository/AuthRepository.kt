package pk.dailymart.customer.repository

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.flow.Flow
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.flow.map
import kotlinx.coroutines.runBlocking
import pk.dailymart.customer.models.ApiResponse
import pk.dailymart.customer.models.FirebaseTokenRequest
import pk.dailymart.customer.models.User
import pk.dailymart.customer.network.ApiService
import pk.dailymart.customer.utils.Resource
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class AuthRepository @Inject constructor(
    private val apiService: ApiService,
    @ApplicationContext private val context: Context,
    private val dataStore: DataStore<Preferences>
) {
    
    companion object {
        private val TOKEN_KEY = stringPreferencesKey("auth_token")
        private val USER_KEY = stringPreferencesKey("user_data")
    }
    
    suspend fun verifyFirebaseToken(firebaseToken: String, name: String?, email: String?): Resource<User> {
        return try {
            val request = FirebaseTokenRequest(firebaseToken, name, email)
            val response = apiService.verifyFirebaseToken(request)
            
            if (response.isSuccessful && response.body()?.success == true) {
                val user = response.body()?.data
                if (user != null) {
                    saveUser(user)
                    response.body()?.token?.let { saveToken(it) }
                    Resource.Success(user)
                } else {
                    Resource.Error("User data is null")
                }
            } else {
                Resource.Error(response.body()?.message ?: "Authentication failed")
            }
        } catch (e: Exception) {
            Resource.Error(e.message ?: "Network error")
        }
    }
    
    suspend fun logout(): Resource<Boolean> {
        return try {
            val response = apiService.logout()
            if (response.isSuccessful && response.body()?.success == true) {
                clearUserData()
                Resource.Success(true)
            } else {
                Resource.Error(response.body()?.message ?: "Logout failed")
            }
        } catch (e: Exception) {
            Resource.Error(e.message ?: "Network error")
        }
    }
    
    suspend fun getCurrentUser(): User? {
        return try {
            val response = apiService.getUser()
            if (response.isSuccessful && response.body()?.success == true) {
                response.body()?.data
            } else {
                null
            }
        } catch (e: Exception) {
            null
        }
    }
    
    suspend fun isLoggedIn(): Boolean {
        val token = dataStore.data.map { preferences ->
            preferences[TOKEN_KEY]
        }.first()
        return token != null
    }
    
    private suspend fun saveUser(user: User) {
        val json = user.toString() // Use Gson for proper serialization
        dataStore.edit { preferences ->
            preferences[USER_KEY] = json
        }
    }
    
    private suspend fun saveToken(token: String) {
        dataStore.edit { preferences ->
            preferences[TOKEN_KEY] = token
        }
    }
    
    private suspend fun clearUserData() {
        dataStore.edit { preferences ->
            preferences.remove(TOKEN_KEY)
            preferences.remove(USER_KEY)
        }
    }
}
