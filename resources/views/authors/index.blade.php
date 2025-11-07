@extends('layouts.app')

@section('title', 'Author Ranking')

@section('content')
<div class="container mt-4">

    <h1 class="mb-3">Author Ranking — {{ ucfirst($type) }}</h1>

    <div class="mb-4">
        <form action="{{ url('/authors') }}" method="get" class="d-flex gap-2" style="max-width: 350px;">
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="popular" @selected($type=='popular')>Most Books (Popular)</option>
                <option value="avg" @selected($type=='avg')>Highest Avg Rating</option>
                <option value="trend" @selected($type=='trend')>Trending Score</option>
            </select>
            <noscript>
                <button class="btn btn-primary">Apply</button>
            </noscript>
        </form>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr class="text-center">
                <th style="width: 80px;">Rank</th>
                <th>Author Name</th>
                <th style="width: 130px;">Total Ratings</th>
                <th style="width: 130px;">Best</th>
                <th style="width: 120px;">Worst</th>
                <th>Trending Score</th>
            </tr>
        </thead>

        <tbody>
            @foreach($authors as $index => $a)
            <tr @if($index==0) style="background: #fff8d6;" @endif>
                <td class="text-center fw-bold">
                    {{ $index + 1 }}
                </td>

                <td>{{ $a->name }}</td>

                <td class="text-center">{{ $a->total_ratings_count }}</td>
                <td class="text-center">{{ number_format($a->best_book_rating, 2) }}</td>
                <td class="text-center">{{ number_format($a->worst_book_rating, 2) }}</td>
                <td>{{ number_format($a->avg_book_rating,2) }}</td>
            </tr>
            @endforeach
        </tbody>

    </table>

</div>
@endsection
