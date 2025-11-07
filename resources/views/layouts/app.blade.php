<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bookstore</title>

    {{-- bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
</head>
<body class="bg-light">

    {{-- NAV --}}
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="{{ route('books.list') }}">Bookstore</a>

            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('books.list')) active @endif"
                       href="{{ route('books.list') }}">Books</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('authors.index')) active @endif"
                       href="{{ route('authors.index') }}">Top Authors</a>
                </li>
            </ul>
        </div>
    </nav>

    {{-- MAIN content --}}
    <div class="container">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
       @stack('scripts')
</body>
</html>
