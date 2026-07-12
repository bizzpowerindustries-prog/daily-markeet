package pk.dailymart.customer.ui.screens

import androidx.compose.foundation.layout.*
import androidx.compose.foundation.lazy.LazyColumn
import androidx.compose.material3.*
import androidx.compose.runtime.*
import androidx.compose.ui.Modifier
import androidx.compose.ui.unit.dp
import androidx.hilt.navigation.compose.hiltViewModel
import coil.compose.AsyncImage
import pk.dailymart.customer.viewmodels.ProductDetailViewModel
import pk.dailymart.customer.ui.components.*

@OptIn(ExperimentalMaterial3Api::class)
@Composable
fun ProductDetailScreen(
    productSlug: String,
    viewModel: ProductDetailViewModel = hiltViewModel(),
    onBack: () -> Unit,
    onAddToCart: (Int) -> Unit
) {
    val productState by viewModel.productState.collectAsState()
    val cartState by viewModel.cartState.collectAsState()
    
    LaunchedEffect(productSlug) {
        viewModel.loadProduct(productSlug)
    }
    
    Scaffold(
        topBar = {
            TopAppBar(
                title = { Text("Product Details") },
                navigationIcon = {
                    IconButton(onClick = onBack) {
                        Icon(Icons.Default.ArrowBack, contentDescription = "Back")
                    }
                }
            )
        },
        bottomBar = {
            if (productState is Resource.Success) {
                ProductBottomBar(
                    product = (productState as Resource.Success).data,
                    onAddToCart = { quantity ->
                        viewModel.addToCart(quantity)
                        onAddToCart((productState as Resource.Success).data.id)
                    },
                    onBuyNow = { /* Handle buy now */ }
                )
            }
        }
    ) { paddingValues ->
        when (productState) {
            is Resource.Loading -> {
                Box(modifier = Modifier.fillMaxSize()) {
                    CircularProgressIndicator(modifier = Modifier.align(Alignment.Center))
                }
            }
            is Resource.Success -> {
                val product = (productState as Resource.Success).data
                LazyColumn(
                    modifier = Modifier
                        .fillMaxSize()
                        .padding(paddingValues)
                ) {
                    // Product Images
                    item {
                        ProductImageCarousel(
                            images = product.images,
                            modifier = Modifier.height(300.dp)
                        )
                    }
                    
                    // Product Info
                    item {
                        ProductInfoSection(product = product)
                    }
                    
                    // Price Section
                    item {
                        PriceSection(
                            price = product.price,
                            salePrice = product.salePrice,
                            discount = calculateDiscount(product.price, product.salePrice)
                        )
                    }
                    
                    // Seller Info
                    item {
                        SellerInfoCard(seller = product.seller)
                    }
                    
                    // Product Specs
                    if (product.specs?.isNotEmpty() == true) {
                        item {
                            SpecsSection(specs = product.specs)
                        }
                    }
                    
                    // Variants
                    if (product.variants?.isNotEmpty() == true) {
                        item {
                            VariantsSection(
                                variants = product.variants,
                                onVariantSelected = { viewModel.selectVariant(it) }
                            )
                        }
                    }
                    
                    // Product Description
                    item {
                        DescriptionSection(description = product.description)
                    }
                    
                    // Reviews
                    item {
                        ReviewsSection(
                            reviews = product.reviews,
                            rating = product.rating,
                            totalReviews = product.review_count
                        )
                    }
                    
                    // Related Products
                    item {
                        RelatedProductsSection(
                            products = product.relatedProducts,
                            onProductClick = { slug ->
                                // Navigate to product detail
                            }
                        )
                    }
                }
            }
            is Resource.Error -> {
                ErrorScreen(message = (productState as Resource.Error).message) {
                    viewModel.loadProduct(productSlug)
                }
            }
        }
    }
}
