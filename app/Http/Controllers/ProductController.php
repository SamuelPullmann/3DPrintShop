<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Requests\DestroyProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search filter - search in name and description
        if ($request->has('q') && !empty($request->q)) {
            $searchTerm = $request->q;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

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

        $products = $query->orderBy('created_at', 'desc')->paginate(12);

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
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();

        // Handle image upload - store in previewImages subdirectory
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('previewImages', 'local');
            $data['file_path'] = $path;
        }

        $product = Product::create($data);

        // Return JSON for API or redirect for web form
        if ($request->wantsJson()) {
            return response()->json($product, 201);
        }

        return redirect()->route('home')->with('success', 'Product added successfully!');
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
    public function update(UpdateProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        $data = $request->validated();

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

        // Return JSON for API or redirect for web form
        if ($request->wantsJson()) {
            return response()->json($product);
        }

        return redirect()->route('home')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyProductRequest $request, string $id)
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
}
