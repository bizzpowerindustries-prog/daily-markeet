<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'brand', 'seller'])
            ->where('status', 'active')
            ->where('is_approved', true);

        // Search
        if ($request->has('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%")
                  ->orWhereHas('brand', function ($q) use ($search) {
                      $q->where('name', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filters
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('min_price')) {
            $query->where('sale_price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('sale_price', '<=', $request->max_price);
        }

        if ($request->has('rating')) {
            $query->where('rating', '>=', $request->rating);
        }

        if ($request->has('in_stock')) {
            $query->where('stock', '>', 0);
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('sale_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('sale_price', 'desc');
                break;
            case 'popularity':
                $query->orderBy('sales_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'discount':
                $query->orderByRaw('(price - sale_price) / price DESC');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $perPage = $request->get('per_page', 20);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $products,
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total()
            ]
        ]);
    }

    public function show($slug)
    {
        $product = Product::with([
            'category',
            'brand',
            'seller',
            'reviews' => function ($query) {
                $query->where('is_approved', true)
                      ->with('user')
                      ->orderBy('created_at', 'desc');
            },
            'variants'
        ])->where('slug', $slug)
          ->where('status', 'active')
          ->where('is_approved', true)
          ->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        // Increment views
        $product->increment('views');

        // Get related products
        $related = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('status', 'active')
            ->where('is_approved', true)
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $product,
            'related_products' => $related
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0|lt:price',
            'stock' => 'required|integer|min:0',
            'images' => 'required|array|min:1|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'specs' => 'nullable|array',
            'variants' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $seller = $user->seller;

        if (!$seller || $seller->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to add products'
            ], 403);
        }

        $product = Product::create([
            'seller_id' => $seller->id,
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name) . '-' . uniqid(),
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'specs' => $request->specs ?? [],
            'is_approved' => false, // Need admin approval
            'status' => 'pending'
        ]);

        // Handle images
        if ($request->has('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                $product->images()->create([
                    'path' => $path,
                    'is_primary' => !$product->images()->exists()
                ]);
            }
        }

        // Handle variants
        if ($request->has('variants')) {
            foreach ($request->variants as $variant) {
                $product->variants()->create($variant);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully. Waiting for admin approval.',
            'data' => $product->load('images', 'variants')
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $user = $request->user();
        $seller = $user->seller;

        if (!$seller || $product->seller_id !== $seller->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to update this product'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'price' => 'sometimes|numeric|min:0',
            'sale_price' => 'sometimes|numeric|min:0',
            'stock' => 'sometimes|integer|min:0',
            'status' => 'sometimes|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $product->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully',
            'data' => $product
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $user = $request->user();
        $seller = $user->seller;

        if (!$seller || $product->seller_id !== $seller->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this product'
            ], 403);
        }

        $product->update(['status' => 'deleted']);

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'success' => true,
                'data' => []
            ]);
        }

        // Search products
        $products = Product::where('status', 'active')
            ->where('is_approved', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhereHas('brand', function ($q) use ($query) {
                      $q->where('name', 'LIKE', "%{$query}%");
                  })
                  ->orWhereHas('category', function ($q) use ($query) {
                      $q->where('name', 'LIKE', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get(['id', 'name', 'slug', 'sale_price', 'images']);

        // Search categories
        $categories = Category::where('name', 'LIKE', "%{$query}%")
            ->limit(3)
            ->get(['id', 'name', 'slug', 'icon']);

        // Search brands
        $brands = Brand::where('name', 'LIKE', "%{$query}%")
            ->limit(3)
            ->get(['id', 'name', 'slug', 'logo']);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'categories' => $categories,
                'brands' => $brands
            ]
        ]);
    }
}
