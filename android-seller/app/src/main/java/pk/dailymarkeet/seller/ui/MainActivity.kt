package pk.dailymarkeet.seller.ui

import android.os.Bundle
import androidx.activity.ComponentActivity
import androidx.activity.compose.setContent
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.MaterialTheme
import androidx.compose.material3.Surface
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.hilt.navigation.compose.hiltViewModel
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.rememberNavController
import dagger.hilt.android.AndroidEntryPoint
import pk.dailymarkeet.seller.ui.screens.*
import pk.dailymarkeet.seller.ui.theme.DailyMarkeetSellerTheme
import pk.dailymarkeet.seller.viewmodels.AuthViewModel

@AndroidEntryPoint
class MainActivity : ComponentActivity() {
    
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContent {
            DailyMarkeetSellerTheme {
                Surface(
                    modifier = Modifier.fillMaxSize(),
                    color = MaterialTheme.colorScheme.background
                ) {
                    SellerAppNavigation()
                }
            }
        }
    }
}

@Composable
fun SellerAppNavigation() {
    val navController = rememberNavController()
    val authViewModel: AuthViewModel = hiltViewModel()
    val authState by authViewModel.authState.collectAsState()
    
    val startDestination = if (authViewModel.isLoggedIn()) "dashboard" else "login"
    
    NavHost(
        navController = navController,
        startDestination = startDestination
    ) {
        composable("login") {
            SellerLoginScreen(
                onLoginSuccess = {
                    navController.navigate("dashboard") {
                        popUpTo("login") { inclusive = true }
                    }
                }
            )
        }
        
        composable("dashboard") {
            DashboardScreen(
                onProductClick = { productId ->
                    navController.navigate("product/$productId")
                },
                onOrderClick = { orderId ->
                    navController.navigate("order/$orderId")
                }
            )
        }
        
        composable("products") {
            ProductManagementScreen(
                onAddProduct = {
                    navController.navigate("add-product")
                },
                onEditProduct = { productId ->
                    navController.navigate("edit-product/$productId")
                }
            )
        }
        
        composable("add-product") {
            AddProductScreen(
                onSave = {
                    navController.popBackStack()
                },
                onCancel = {
                    navController.popBackStack()
                }
            )
        }
        
        composable("edit-product/{productId}") { backStackEntry ->
            val productId = backStackEntry.arguments?.getString("productId")?.toIntOrNull() ?: 0
            EditProductScreen(
                productId = productId,
                onSave = {
                    navController.popBackStack()
                },
                onCancel = {
                    navController.popBackStack()
                }
            )
        }
        
        composable("orders") {
            OrdersScreen(
                onOrderClick = { orderId ->
                    navController.navigate("order/$orderId")
                }
            )
        }
        
        composable("order/{orderId}") { backStackEntry ->
            val orderId = backStackEntry.arguments?.getString("orderId")?.toIntOrNull() ?: 0
            OrderDetailScreen(
                orderId = orderId,
                onBack = {
                    navController.popBackStack()
                }
            )
        }
        
        composable("wallet") {
            WalletScreen()
        }
        
        composable("profile") {
            ProfileScreen()
        }
    }
}
