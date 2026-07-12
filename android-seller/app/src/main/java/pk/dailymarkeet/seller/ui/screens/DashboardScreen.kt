package pk.dailymarkeet.seller.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Alignment
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import pk.dailymarkeet.seller.viewmodels.DashboardViewModel
import pk.dailymarkeet.seller.utils.Resource
import pk.dailymarkeet.seller.ui.components.*

@Composable
fun DashboardScreen(
    onProductClick: (Int) -> Unit,
    onOrderClick: (Int) -> Unit,
    viewModel: DashboardViewModel = hiltViewModel()
) {
    val dashboardState by viewModel.dashboardState.collectAsState()
    
    Scaffold(
        topBar = {
            TopAppBar(
                title = {
                    Text(
                        "DailyMarkeet",
                        color = MaterialTheme.colorScheme.primary
                    )
                },
                actions = {
                    IconButton(onClick = { /* Refresh */ }) {
                        Icon(Icons.Default.Refresh, contentDescription = "Refresh")
                    }
                }
            )
        },
        bottomBar = {
            SellerBottomNavigationBar()
        }
    ) { paddingValues ->
        when (dashboardState) {
            is Resource.Loading -> {
                Box(modifier = Modifier.fillMaxSize()) {
                    CircularProgressIndicator(modifier = Modifier.align(Alignment.Center))
                }
            }
            is Resource.Success -> {
                val data = (dashboardState as Resource.Success).data
                LazyColumn(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues),
                    verticalArrangement = Arrangement.spacedBy(16.dp)
                ) {
                    // Stats Cards
                    item {
                        StatsRow(stats = data.stats)
                    }
                    
                    // Quick Actions
                    item {
                        QuickActionsRow(
                            onAddProduct = { /* Navigate to add product */ },
                            onViewOrders = { /* Navigate to orders */ }
                        )
                    }
                    
                    // Wallet Summary
                    item {
                        WalletSummaryCard(
                            balance = data.walletBalance,
                            todayEarnings = data.todayEarnings,
                            onWithdraw = { /* Navigate to withdraw */ }
                        )
                    }
                    
                    // Recent Orders
                    item {
                        SectionHeader(title = "Recent Orders")
                    }
                    
                    items(data.recentOrders) { order ->
                        OrderCard(
                            order = order,
                            onUpdateStatus = { orderId, status ->
                                viewModel.updateOrderStatus(orderId, status)
                            },
                            onClick = { onOrderClick(order.id) }
                        )
                    }
                    
                    // Top Products
                    if (data.topProducts.isNotEmpty()) {
                        item {
                            SectionHeader(title = "Top Products")
                        }
                        
                        items(data.topProducts) { product ->
                            ProductPerformanceCard(product = product)
                        }
                    }
                }
            }
            is Resource.Error -> {
                Box(modifier = Modifier.fillMaxSize(), contentAlignment = Alignment.Center) {
                    Column(horizontalAlignment = Alignment.CenterHorizontally) {
                        Text(
                            text = "Error: ${(dashboardState as Resource.Error).message}",
                            color = MaterialTheme.colorScheme.error
                        )
                        Spacer(modifier = Modifier.height(16.dp))
                        Button(onClick = { viewModel.loadDashboard() }) {
                            Text("Retry")
                        }
                    }
                }
            }
        }
    }
}
