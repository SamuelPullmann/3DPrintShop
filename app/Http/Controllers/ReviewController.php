<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Http\Requests\DestroyReviewRequest;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController
{
    /**
     * Display a listing of reviews for a product
     */
    public function index($productId)
    {
        $reviews = Review::where('product_id', $productId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($reviews);
    }

    /**
     * Store a newly created review
     */
    public function store(StoreReviewRequest $request)
    {
        $validated = $request->validated();

        // Create the review
        $review = Review::create([
            'product_id' => $validated['product_id'],
            'user_id' => auth()->id(),
            'rating' => $validated['rating'],
            'review_text' => $validated['review_text'],
        ]);

        // Load user relationship
        $review->load('user');

        return response()->json([
            'message' => 'Review submitted successfully!',
            'review' => $review
        ], 201);
    }

    /**
     * Display the specified review
     */
    public function show($id)
    {
        $review = Review::with('user')->findOrFail($id);
        return response()->json($review);
    }

    /**
     * Update the specified review
     */
    public function update(UpdateReviewRequest $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->update($request->validated());

        return response()->json([
            'message' => 'Review updated successfully!'
        ]);
    }

    /**
     * Remove the specified review
     */
    public function destroy(DestroyReviewRequest $request, $id)
    {
        $review = Review::findOrFail($id);
        $review->delete();

        return response()->json([
            'message' => 'Review deleted successfully!'
        ]);
    }
}
