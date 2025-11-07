@extends('layouts.app')

@section('content')
    <h2 class="mb-4">Book List</h2>
    <form method="GET" action="{{ route('books.list') }}" class="mb-4">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">Filter & Sort Options</h5>
            </div>
           @include('books.filters')
        </div>
    </form>


    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Book Results</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 25%;">Title</th>
                        <th style="width: 15%;">Author</th>
                        <th style="width: 20%;">Categories</th>
                        <th style="width: 15%;">ISBN</th>
                        <th>Avg Rating</th>
                        <th>Total Votes</th>
                        <th>Rating</th>
                        <th>Trending</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($books as $b)
                        <tr>
                            <td>{{ $b->title }}</td>
                            <td>{{ $b->author->name }}</td>
                            <td>
                                @foreach($b->categories as $category)
                                    <span class="badge bg-secondary rounded-pill me-1 mb-1">{{ $category->name }}</span>
                                @endforeach
                            </td>
                            <td>{{ $b->isbn }}</td>
                            <td>{{ number_format($b->avg_rating, 2) }}</td>
                            <td>{{ number_format($b->total_votes) }}</td>
                            <td>
                                <a href="{{ url('/books/'.$b->id.'/rate') }}" class="btn btn-sm btn-warning">
                                    Rate
                                </a>
                            </td>
                            <td class="text-center">
                                @if($b->is_trending)
                                    <span class="text-success fw-bold fs-5" title="Trending Up - Rating improved in last 7 days">↑</span>
                                @else
                                    <span class="text-muted" title="Stable or Declining">−</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $b->availability == 'available' ? 'bg-success' : ($b->availability == 'rented' ? 'bg-warning' : 'bg-info') }}">
                                    {{ ucfirst($b->availability) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No books found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{-- Bagian Pagination (dibuat ke tengah) --}}
    <div class="my-4 d-flex justify-content-center">
        {{ $books->links() }}
    </div>
@endsection
