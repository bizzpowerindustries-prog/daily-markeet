package pk.dailymarkeet.seller.repository

import android.content.Context
import androidx.datastore.core.DataStore
import androidx.datastore.preferences.core.Preferences
import androidx.datastore.preferences.core.edit
import androidx.datastore.preferences.core.stringPreferencesKey
import com.google.gson.Gson
import dagger.hilt.android.qualifiers.ApplicationContext
import kotlinx.coroutines.flow.first
import kotlinx.coroutines.flow.map
import pk.dailymarkeet.seller.models.User
import pk.dailymarkeet.seller.network.ApiService
import javax.inject.Inject
import javax.inject.Singleton

@Singleton
class AuthRepository @Inject constructor(
    private val apiService: ApiService,
    @ApplicationContext private val context: Context,
    private val dataStore: DataStore<Preferences>
) {
    companion object {
        private val TOKEN_KEY = stringPreferencesKey("seller_auth_token")
        private val USER_KEY = stringPreferencesKey("seller_user_data")
    }
    
    suspend fun verifyFirebaseToken(firebaseToken: String): User {
        val request = mapOf("firebase_token" to firebaseToken)
        val response = apiService.verifyFirebaseToken(request)
        
        if (response.isSuccessful && response.body()?.success == true) {
            val user = response.body()?.data
            user?.let { saveUser(it) }
            response.body()?.token?.let { saveToken(it) }
            return user!!
        }
        
        throw Exception(response.body()?.message ?: "Authentication failed")
    }
    
    suspend fun logout() {
        try {
            apiService.logout()
        } finally {
            clearUserData()
        }
    }
    
    suspend fun isLoggedIn(): Boolean {
        return dataStore.data.map { preferences ->
            preferences[TOKEN_KEY] != null
        }.first()
    }
    
    private suspend fun saveUser(user: User) {
        val json = Gson().toJson(user)
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
