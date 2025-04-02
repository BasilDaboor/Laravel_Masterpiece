<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Provider;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews.
     */
    public function index()
    {
        $reviews = Review::with(['user', 'provider.user'])
            ->latest()
            ->paginate(15);

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new review.
     */
    public function create(Request $request)
    {
        $booking = null;
        $provider = null;

        if ($request->has('booking_id')) {
            $booking = Booking::with('provider')->findOrFail($request->booking_id);
            $provider = $booking->provider;
        } elseif ($request->has('provider_id')) {
            $provider = Provider::findOrFail($request->provider_id);
        }

        return view('reviews.create', compact('booking', 'provider'));
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'booking_id' => 'nullable|exists:bookings,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        // Use authenticated user as reviewer
        $validated['user_id'] = Auth::id();

        // Remove booking_id as it's not part of the reviews table
        unset($validated['booking_id']);

        $review = Review::create($validated);

        // Update provider's average rating
        $provider = Provider::find($validated['provider_id']);
        $avgRating = Review::where('provider_id', $provider->id)->avg('rating');
        $provider->update(['rating' => $avgRating]);

        return redirect()->route('reviews.show', $review)->with('success', 'Review submitted successfully!');
    }

    /**
     * Display the specified review.
     */
    public function show(Review $review)
    {
        $review->load(['user', 'provider.user']);
        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified review.
     */
    public function edit(Review $review)
    {
        // Check if user is authorized to edit this review

        // if (Auth::id() !== $review->user_id && !Auth::user()->isAdmin()) {
        //     return abort(403);
        // }

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified review in storage.
     */
    public function update(Request $request, Review $review)
    {
        // Check if user is authorized to update this review

        // if (Auth::id() !== $review->user_id && !Auth::user()->isAdmin()) {
        //     return abort(403);
        // }

        $validated = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
        ]);

        $review->update($validated);

        // Update provider's average rating
        $provider = Provider::find($review->provider_id);
        $avgRating = Review::where('provider_id', $provider->id)->avg('rating');
        $provider->update(['rating' => $avgRating]);

        return redirect()->route('reviews.show', $review)->with('success', 'Review updated successfully!');
    }

    /**
     * Remove the specified review from storage.
     */
    public function destroy(Review $review)
    {
        // Check if user is authorized to delete this review

        // if (Auth::id() !== $review->user_id && !Auth::user()->isAdmin()) {
        //     return abort(403);
        // }

        $providerId = $review->provider_id;

        $review->delete();

        // Update provider's average rating
        $provider = Provider::find($providerId);
        $avgRating = Review::where('provider_id', $providerId)->avg('rating') ?? 0;
        $provider->update(['rating' => $avgRating]);

        return redirect()->route('reviews.index')->with('success', 'Review deleted successfully!');
    }
}
