package pk.dailymart.customer.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.foundation.lazy.LazyRow
import androidx.compose.foundation.lazy.items
import androidx.compose.material3.*
import androidx.compose.runtime.Composable
import androidx.compose.runtime.collectAsState
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import pk.dailymart.customer.viewmodels.HomeViewModel
import pk.dailymart.customer.ui.components.*

@Composable
fun HomeScreen(
    viewModel: HomeViewModel = hiltViewModel(),
    onProductClick: (String) -> Unit,
    onCategoryClick: (String) -> Unit,
    onSearchClick: () -> Unit
) {
    val homeData by viewModel.homeData.collectAsState()
    
    Scaffold(
        topBar = { HomeTopBar(onSearchClick = onSearchClick) },
        bottomBar = { BottomNavigationBar() }
    ) { paddingValues ->
        when (homeData) {
            is Resource.Loading -> {
                Box(modifier = Modifier.fillMaxSize()) {
                    CircularProgressIndicator(modifier = Modifier.align(Alignment.Center))
                }
            }
            is Resource.Success -> {
                val data = (homeData as Resource.Success).data
                LazyColumn(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues),
                    verticalArrangement = Arrangement.spacedBy(8.dp)
                ) {
                    // Hero Slider
                    item {
                        HeroSlider(
                            slides = data.banners,
                            modifier = Modifier.height(200.dp)
                        )
                    }
                    
                    // Categories Grid
                    item {
                        CategoryGrid(
                            categories = data.categories,
                            onCategoryClick = onCategoryClick
                        )
                    }
                    
                    // Flash Sale
                    if (data.flashSale != null) {
                        item {
                            FlashSaleSection(
                                flashSale = data.flashSale,
                                onProductClick = onProductClick
                            )
                        }
                    }
                    
                    // Sponsored Products
                    item {
                        SectionTitle(title = "Sponsored Products")
                        LazyRow(
                            horizontalArrangement = Arrangement.spacedBy(8.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            items(data.sponsoredProducts) { product ->
                                ProductCard(
                                    product = product,
                                    onClick = { onProductClick(product.slug) }
                                )
                            }
                        }
                    }
                    
                    // Just For You
                    item {
                        SectionTitle(title = "Just For You")
                        LazyRow(
                            horizontalArrangement = Arrangement.spacedBy(8.dp),
                            modifier = Modifier.fillMaxWidth()
                        ) {
                            items(data.justForYou) { product ->
                                ProductCard(
                                    product = product,
                                    onClick = { onProductClick(product.slug) }
                                )
                            }
                        }
                    }
                    
                    // Trending Products
                    item {
                        SectionTitle(title = "Trending Products")
                        TrendingProductsGrid(
                            products = data.trending,
                            onProductClick = onProductClick
                        )
                    }
                }
            }
            is Resource.Error -> {
                ErrorScreen(message = (homeData as Resource.Error).message) {
                    viewModel.loadHomeData()
                }
            }
        }
    }
}
