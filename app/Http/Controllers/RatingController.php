<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Rating;

class RatingController extends Controller
{
    public function rateForm($id)
    {
        $book = Book::with('author')->findOrFail($id);
        return view('books.rate', compact('book'));
    }

    public function rateStore(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:10'
        ]);

        $userId = session('user_identifier');

        $lastRating = Rating::where('user_identifier', $userId)
                        ->latest()
                        ->first();

        if ($lastRating && $lastRating->created_at->diffInHours(now()) < 24) {
            return redirect()->back()->with('error', 'You must wait 24 hours before rating again.');
        }

        Rating::create([
            'book_id' => $id,
            'user_identifier' => $userId,
            'rating' => $request->rating
        ]);

        // update book.avg_rating via eloquent
        $avg = Rating::where('book_id', $id)->avg('rating');

        Book::where('id', $id)->update([
            'avg_rating' => $avg
        ]);

        return redirect()->route('books.list')->with('success','Rating submitted!');
    }
}
