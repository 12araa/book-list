<?php

namespace App\Http\Controllers;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use Illuminate\Http\Request;

class BookController extends Controller
{
    /**
     * Menampilkan daftar buku (halaman utama)
     */
    public function index(Request $request)
    {
        $query = Book::query();

        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = $request->input('search');

            $q->where(function ($subQ) use ($search) {
                $subQ->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('isbn', 'LIKE', "%{$search}%")
                    ->orWhere('publisher', 'LIKE', "%{$search}%")
                    ->orWhereHas('author', function ($authorQ) use ($search) {
                        $authorQ->where('name', 'LIKE', "%{$search}%");
                    });
            });
        });

        // 2. LOGIKA FILTER (Filter Spesifik)
        // Filter by Author
        $query->when($request->filled('author_id'), function ($q) use ($request) {
            $q->where('author_id', $request->author_id);
        });

        // Filter by Availability
        $query->when($request->filled('availability'), function ($q) use ($request) {
            $q->where('availability', $request->availability);
        });

        // Filter by Store Location
        $query->when($request->filled('store_location'), function ($q) use ($request) {
            $q->where('store_location', 'LIKE', "%" . $request->store_location . "%");
        });

        // Filter by Rating Range
        $query->when($request->filled('min_rating'), function ($q) use ($request) {
            $q->where('avg_rating', '>=', $request->min_rating);
        });
        $query->when($request->filled('max_rating'), function ($q) use ($request) {
            $q->where('avg_rating', '<=', $request->max_rating);
        });

        // Filter by Publication Year Range (BARU)
        $query->when($request->filled('min_year'), function ($q) use ($request) {
            $q->where('publication_year', '>=', $request->min_year);
        });
        $query->when($request->filled('max_year'), function ($q) use ($request) {
            $q->where('publication_year', '<=', $request->max_year);
        });

        // Filter by Category (AND/OR Logic)
        $selectedCategoryIds = $request->input('categories', []);
        $catLogic = $request->input('cat_logic', 'or');

        $query->when(!empty($selectedCategoryIds), function ($q) use ($selectedCategoryIds, $catLogic) {
            $q->whereHas('categories', function ($catQ) use ($selectedCategoryIds) {
                $catQ->whereIn('categories.id', $selectedCategoryIds);
            },
            $catLogic === 'and' ? '>=' : '>=',
            $catLogic === 'and' ? count($selectedCategoryIds) : 1
            );
        });

        // 3. LOGIKA SORTING
        $sort = $request->input('sort');

        if ($sort === 'recent') {
            $query->withCount(['ratings as recent_votes' => function($q){
                $q->where('created_at', '>=', now()->subDays(30));
            }])->orderByDesc('recent_votes');

        } elseif ($sort === 'votes') {
            $query->orderByDesc('total_votes');
        } elseif ($sort === 'alpha') {
            $query->orderBy('title', 'asc');
        } elseif ($sort === 'year') {
             $query->orderByDesc('publication_year');
        } else {
            $query->orderByDesc('weighted_rating');
        }

        // 4. EAGER LOADING & PAGINATION
        $books = $query->with('author', 'categories')
                      ->simplePaginate(50)
                      ->appends($request->query());


        // 5. TRENDING
        // $now   = now();
        // $last7 = $now->clone()->subDays(7);
        // $prev7 = $now->clone()->subDays(14);

        // // get all book_ids in this page
        // $bookIds = collect($books->items())->pluck('id')->toArray();

        // // get count votes last 7 days
        // $recentVotes = DB::table('ratings')
        //     ->select('book_id', DB::raw('COUNT(*) as cnt'))
        //     ->where('created_at', '>=', $last7)
        //     ->groupBy('book_id');

        // // 3. Buat query untuk menghitung vote MINGGU LALU
        // $previousVotes = DB::table('ratings')
        //     ->select('book_id', DB::raw('COUNT(*) as cnt'))
        //     ->whereBetween('created_at', [$prev7, $last7])
        //     ->groupBy('book_id');

        // // 4. Gabungkan semua dalam SATU UPDATE QUERY
        // DB::table('books')
        //     ->leftJoinSub($recentVotes, 'recent', function ($join) {
        //         $join->on('books.id', '=', 'recent.book_id');
        //     })
        //     ->leftJoinSub($previousVotes, 'previous', function ($join) {
        //         $join->on('books.id', '=', 'previous.book_id');
        //     })
        //     ->update([
        //         // COALESCE() untuk mengubah NULL (jika tidak ada vote) menjadi 0
        //         'rating_trend_flag' => DB::raw('CASE WHEN COALESCE(recent.cnt, 0) > COALESCE(previous.cnt, 0) THEN 1 ELSE 0 END')
        //     ]);

        // $this->info('Trending flags updated successfully!');




        // PERSIAPAN DATA UNTUK VIEW
        $authors = Author::orderBy('name')->get();

        $selectedCategories = [];
        if (!empty($selectedCategoryIds)) {
            $selectedCategories = Category::whereIn('id', $selectedCategoryIds)
                                        ->select('id', 'name')
                                        ->get();
        }

        $selectedAuthor = null;
        if ($request->filled('author_id')) {
            $selectedAuthor = Author::select('id', 'name')->find($request->author_id);
        }

        $selectedCategoryIds = $request->input('categories', []);
        $selectedCategories = [];
        if (!empty($selectedCategoryIds)) {
            $selectedCategories = Category::whereIn('id', $selectedCategoryIds)
                                        ->select('id', 'name')
                                        ->get();
        }

        return view('books.index', compact(
            'books',
            'selectedAuthor',
            'selectedCategories'
        ));
    }

    public function search(Request $request)
    {
        $searchQuery = $request->input('q');

        if (!$searchQuery) {
            return response()->json([]);
        }

        $categories = Category::where('name', 'LIKE', "%{$searchQuery}%")
                            ->select('id', 'name as text')
                            ->limit(20)
                            ->get();

        return response()->json($categories);
    }
}
