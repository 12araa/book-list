@extends('layouts.app')

@section('content')
<div class="container mt-4">

    <h3>Rate Book</h3>

    <p><strong>{{ $book->title }}</strong><br>
    by {{ $book->author->name }}</p>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form method="POST" action="{{ url('/books/'.$book->id.'/rate') }}">
        @csrf

        <label>Rating:</label>
        <select name="rating" class="form-select" required style="max-width: 200px;">
            @for($i=1; $i<=10; $i++)
                <option value="{{ $i }}">{{ $i }}</option>
            @endfor
        </select>

        <button class="btn btn-primary mt-3">Submit</button>
    </form>

</div>
@endsection
