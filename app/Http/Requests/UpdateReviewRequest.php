<?php

namespace App\Http\Requests;

use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class UpdateReviewRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User musí byť prihlásený
        if (!auth()->check()) {
            return false;
        }

        // Get the review from route parameter
        $reviewId = $this->route('review');
        $review = Review::find($reviewId);

        if (!$review) {
            return false;
        }

        // User can update if they own the review or are admin
        return $review->user_id === auth()->id() || auth()->user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'review_text' => 'sometimes|required|string|max:1000',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'rating.required' => 'Rating is required.',
            'rating.integer' => 'Rating must be a number.',
            'rating.min' => 'Rating must be at least 1.',
            'rating.max' => 'Rating cannot be more than 5.',
            'review_text.required' => 'Review text is required.',
            'review_text.max' => 'Review text cannot exceed 1000 characters.',
        ];
    }
}
