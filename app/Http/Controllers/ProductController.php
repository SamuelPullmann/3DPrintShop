<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter by product type (digital/physical) - only if checkboxes are selected
        if ($request->has('type') && is_array($request->type) && count($request->type) > 0) {
            $types = array_map(function($t) {
                return ucfirst(strtolower($t));
            }, $request->type);
            // Include products with matching type OR null type
            $query->where(function($q) use ($types) {
                $q->whereIn('product_type', $types)
                  ->orWhereNull('product_type');
            });
        }

        // Filter by categories - only if checkboxes are selected
        if ($request->has('cat') && is_array($request->cat) && count($request->cat) > 0) {
            // Include products with matching category OR null category
            $query->where(function($q) use ($request) {
                $q->whereIn('category', $request->cat)
                  ->orWhereNull('category');
            });
        }

        // Get max price for filter range
        $maxPrice = Product::max('price') ?? 100;
        $maxPrice = ceil($maxPrice); // Round up to nearest integer

        // Filter by price range
        if ($request->has('price_min') && $request->price_min > 0) {
            $query->where('price', '>=', $request->price_min);
        }
        if ($request->has('price_max') && $request->price_max < $maxPrice) {
            $query->where('price', '<=', $request->price_max);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(9);

        // If it's an AJAX request, return JSON
        if ($request->wantsJson()) {
            return response()->json([
                'data' => $products->items(),
                'max_price' => $maxPrice,
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ]);
        }

        // Otherwise, return the view
        return view('home', compact('products', 'maxPrice'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'product_type' => 'required|in:Digital,Physical',
            'category' => 'nullable|string|in:miniatures,architecture,art,functional,toys',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();


        // Handle image upload - store in previewImages subdirectory
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('previewImages', 'local');
            $data['file_path'] = $path;
        }

        $product = Product::create($data);

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    /**
     * Display the product details page.
     */
    public function showPage(string $id)
    {
        $product = Product::with(['reviews.user'])->findOrFail($id);

        // Calculate average rating and count
        $averageRating = $product->reviews->avg('rating');
        $reviewsCount = $product->reviews->count();

        return view('product-details', compact('product', 'averageRating', 'reviewsCount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'product_type' => 'sometimes|required|in:Digital,Physical',
            'category' => 'nullable|string|in:miniatures,architecture,art,functional,toys',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();


        // Handle image upload - store in previewImages subdirectory
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->file_path && Storage::disk('local')->exists($product->file_path)) {
                Storage::disk('local')->delete($product->file_path);
            }

            $path = $request->file('image')->store('previewImages', 'local');
            $data['file_path'] = $path;
        }

        $product->update($data);

        return response()->json($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);

        // Delete image if exists
        if ($product->file_path && Storage::disk('local')->exists($product->file_path)) {
            Storage::disk('local')->delete($product->file_path);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    /**
     * Serve product image
     */
    public function image(string $id)
    {
        $product = Product::findOrFail($id);

        if (!$product->file_path || !Storage::disk('local')->exists($product->file_path)) {
            // Return a 1x1 transparent PNG as placeholder instead of 404
            $placeholder = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');
            return response($placeholder, 200)->header('Content-Type', 'image/png');
        }

        $path = Storage::disk('local')->path($product->file_path);

        return response()->file($path);
    }

    /**
     * Store a new review
     */
    public function storeReview(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,product_id',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user is authenticated
        if (!auth()->check()) {
            return response()->json(['error' => 'You must be logged in to write a review'], 401);
        }

        // Create the review
        $review = \App\Models\Review::create([
            'product_id' => $request->product_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'review_text' => $request->review_text,
        ]);

        // Load user relationship
        $review->load('user');

        return response()->json([
            'message' => 'Review submitted successfully!',
            'review' => $review
        ], 201);
    }

    /**
     * Delete a review (Admin only)
     */
    public function deleteReview($reviewId)
    {
        // Check if user is admin
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $review = \App\Models\Review::find($reviewId);

        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }

        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully!'
        ], 200);
    }
}
