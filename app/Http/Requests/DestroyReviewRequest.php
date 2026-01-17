<?php

namespace App\Http\Requests;

use App\Models\Review;
use Illuminate\Foundation\Http\FormRequest;

class DestroyReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (!auth()->check()) {
            return false;
        }

        $review = Review::find($this->route('review'));

        if (!$review) {
            return false;
        }

        return $review->user_id === auth()->id() || auth()->user()->role === 'admin';
    }
}
