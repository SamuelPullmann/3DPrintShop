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
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(9);

        // If it's an AJAX request, return JSON
        if (request()->wantsJson()) {
            return response()->json($products);
        }

        // Otherwise, return the view
        return view('home', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
            'category' => 'nullable|in:miniatures,architecture,art,functional,toys',
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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
            'category' => 'nullable|in:miniatures,architecture,art,functional,toys',
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
            abort(404, 'Image not found');
        }

        $path = Storage::disk('local')->path($product->file_path);

        return response()->file($path);
    }
}
