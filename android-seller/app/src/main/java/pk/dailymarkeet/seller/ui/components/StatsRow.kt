package pk.dailymarkeet.seller.ui.components

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import pk.dailymarkeet.seller.models.DashboardStats

@Composable
fun StatsRow(stats: DashboardStats) {
    LazyRow(
        horizontalArrangement = Arrangement.spacedBy(8.dp),
        modifier = Modifier
            .fillMaxWidth()
            .padding(horizontal = 16.dp)
    ) {
        item {
            StatsCard(
                title = "Total Orders",
                value = stats.totalOrders.toString(),
                icon = Icons.Default.ShoppingCart
            )
        }
        item {
            StatsCard(
                title = "Pending",
                value = stats.pendingOrders.toString(),
                icon = Icons.Default.Pending,
                color = MaterialTheme.colorScheme.warning
            )
        }
        item {
            StatsCard(
                title = "Products",
                value = stats.totalProducts.toString(),
                icon = Icons.Default.Inventory
            )
        }
        item {
            StatsCard(
                title = "Earnings",
                value = "Rs. ${stats.totalEarnings}",
                icon = Icons.Default.Money
            )
        }
    }
}

@Composable
fun StatsCard(
    title: String,
    value: String,
    icon: ImageVector,
    color: Color = MaterialTheme.colorScheme.primary,
    modifier: Modifier = Modifier
) {
    Card(
        modifier = modifier.width(120.dp),
        elevation = CardDefaults.cardElevation(defaultElevation = 2.dp)
    ) {
        Column(
            modifier = Modifier
                .fillMaxWidth()
                .padding(12.dp)
        ) {
            Icon(
                imageVector = icon,
                contentDescription = null,
                tint = color,
                modifier = Modifier.size(24.dp)
            )
            Spacer(modifier = Modifier.height(4.dp))
            Text(
                text = value,
                style = MaterialTheme.typography.titleMedium,
                maxLines = 1
            )
            Text(
                text = title,
                style = MaterialTheme.typography.bodySmall,
                color = MaterialTheme.colorScheme.onSurface.copy(alpha = 0.6f),
                maxLines = 1
            )
        }
    }
}
