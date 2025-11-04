<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Item $item)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user already reviewed this item
        $existingReview = Review::where('user_id', Auth::id())
            ->where('item_id', $item->id)
            ->first();

        if ($existingReview) {
            $existingReview->update($validated);
            return redirect()->back()->with('success', 'Avaliação atualizada com sucesso!');
        }

        Review::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
        ]);

        return redirect()->back()->with('success', 'Avaliação adicionada com sucesso!');
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Review $review)
    {
        // Verify the review belongs to the authenticated user
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $review->update($validated);

        return redirect()->back()->with('success', 'Avaliação atualizada com sucesso!');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        // Verify the review belongs to the authenticated user
        if ($review->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Acesso negado.');
        }

        $review->delete();

        return redirect()->back()->with('success', 'Avaliação removida com sucesso!');
    }

    /**
     * Get reviews for an item.
     */
    public function getItemReviews(Item $item)
    {
        $reviews = $item->reviews()->with('user')->recent()->get();
        $averageRating = $item->reviews()->avg('rating');
        $reviewCount = $item->reviews()->count();

        return response()->json([
            'reviews' => $reviews,
            'average_rating' => round($averageRating, 1),
            'review_count' => $reviewCount,
        ]);
    }
}

