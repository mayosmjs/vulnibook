<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // Store a new review for a book
    public function store(Request $request, $bookId)
    {
        $payload = $request->get('auth_user');

        if (!isset($payload['id'])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $review = Review::create([
            'user_id' => $payload['id'],
            'book_id' => $bookId,
            'content' => $request->input('content'),
            'rating' => $request->input('rating', 5), // Default rating to 5 if not provided
        ]);

        return response()->json(['message' => 'Review created', 'review' => $review]);
    }



    public function approve(Request $request, $id)
    {
        $review = Review::find($id);

        // dd($review); // 
    
        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }
    
        // Extract and decode JWT manually (vulnerable)
        $authHeader = $request->header('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'No token provided'], 401);
        }
    
        $jwt = str_replace('Bearer ', '', $authHeader);
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            return response()->json(['error' => 'Invalid token format'], 401);
        }
    
        $payload = json_decode(base64_decode($parts[1]), true);
    
        // No signature verification  fully trust the payload! (vulnerable)
        $role = $payload['role'] ?? 'user';
    
        if ($role !== 'admin') {
            return response()->json(['error' => 'Only admins can approve reviews'], 403);
        }
    
        $review->approved = true; 
        $review->save();        
    
        return response()->json([
            'message' => 'Review approved',
        ]);
    }
    

    // Delete a review
    public function destroy(Request $request, $id)
    {
        $payload = $request->get('auth_user');

        if (!isset($payload['id'])) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $review = Review::find($id);
        if (!$review) {
            return response()->json(['error' => 'Review not found'], 404);
        }

        if ($review->user_id !== $payload['id']) {
            return response()->json(['error' => 'Unauthorized – You can only delete your own reviews'], 403);
        }

        $review->delete();

        return response()->json(['message' => 'Review deleted']);
    }
}
