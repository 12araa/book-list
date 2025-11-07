<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthorController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type', 'popular'); // default popular

        // base query
        $query = \App\Models\Author::query()
            ->select('authors.*')
            ->withCount(['books as total_books'])
            ->withAvg('books as avg_book_rating', 'avg_rating')
            ->withCount(['ratedBooks as total_ratings_count'])
            ->withMax('ratedBooks as best_book_rating', 'rating')
            ->withMin('ratedBooks as worst_book_rating', 'rating');


        if ($type === 'popular') {
            $query->orderByDesc('total_books');
        } elseif ($type === 'avg') {
            $query->orderByDesc('avg_book_rating');
        } elseif ($type === 'trend') {
            $query->orderByRaw('(avg_book_rating * (ln(total_ratings_count + 1))) DESC');
        }

        $authors = $query->limit(20)->get();

        return view('authors.index', compact('authors', 'type'));
    }
}

