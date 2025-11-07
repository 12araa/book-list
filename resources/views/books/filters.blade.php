<form method="GET" action="{{ route('books.list') }}" class="mb-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">

            <!-- BARIS 1: PENCARIAN UTAMA (Dari optimasi sebelumnya) -->
            <div class="row g-3 mb-3">
                <div class="col-12">
                    <label for="search" class="form-label fw-bold">Search</label>
                    <input type="text" name="search" id="search" class="form-control form-control-lg"
                           value="{{ request('search') }}"
                           placeholder="Search by Title, Author, ISBN, or Publisher...">
                </div>
            </div>

            <hr>

            <!-- BARIS 2: FILTER UTAMA (Layout 4+4+4) -->
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="availability" class="form-label">Availability</label>
                    <select name="availability" id="availability" class="form-select">
                        <option value="">-- All --</option>
                        <option value="available" @selected(request('availability') == 'available')>Available</option>
                        <option value="rented" @selected(request('availability') == 'rented')>Rented</option>
                        <option value="reserved" @selected(request('availability') == 'reserved')>Reserved</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="sort" class="form-label">Sort By</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="" @selected(request('sort') == '')>Weighted Rating (Default)</option>
                        <option value="votes" @selected(request('sort') == 'votes')>Total Votes</option>
                        <option value="recent" @selected(request('sort') == 'recent')>Recent Popularity (30 days)</option>
                        <option value="alpha" @selected(request('sort') == 'alpha')>Alphabetical</option>
                        <option value="rating" @selected(request('sort') == 'rating')>Highest Rating</option>
                        <option value="year" @selected(request('sort') == 'year')>Newest Published</option>
                    </select>
                </div>
            </div>

            <!-- BARIS 3: FILTER RENTANG (Layout 3+3+3+3) -->
            <div class="row g-3 mt-2">
                <div class="col-md-3">
                    <label for="min_rating" class="form-label">Min Rating</label>
                    <input type="number" name="min_rating" id="min_rating" class="form-control" step="0.1" value="{{ request('min_rating') }}" placeholder="e.g. 1.0">
                </div>
                <div class="col-md-3">
                    <label for="max_rating" class="form-label">Max Rating</label>
                    <input type="number" name="max_rating" id="max_rating" class="form-control" step="0.1" value="{{ request('max_rating') }}" placeholder="e.g. 10.0">
                </div>
                <div class="col-md-3">
                    <label for="min_year" class="form-label">Min Year</label>
                    <input type="number" name="min_year" id="min_year" class="form-control" step="1" value="{{ request('min_year') }}" placeholder="e.g. 1990">
                </div>
                <div class="col-md-3">
                    <label for="max_year" class="form-label">Max Year</label>
                    <input type="number" name="max_year" id="max_year" class="form-control" step="1" value="{{ request('max_year') }}" placeholder="e.g. 2025">
                </div>
            </div>

            <!-- BARIS 4: FILTER KATEGORI & LOKASI (Layout 6+3+3) -->
            <div class="row g-3 mt-2">
                <div class="col-md-6">
                    <label for="categories" class="form-label">Categories (Type to search)</label>
                    <select name="categories[]" id="categories" class="form-control" multiple
                            placeholder="Type to search categories...">
                        @if (request('categories'))
                            @foreach ($selectedCategories as $category)
                                <option value="{{ $category->id }}" selected>{{ $category->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="cat_logic" class="form-label">Category Logic</label>
                    <select name="cat_logic" id="cat_logic" class="form-select">
                        <option value="or" @selected(request('cat_logic', 'or') == 'or')>Match ANY (OR)</option>
                        <option value="and" @selected(request('cat_logic') == 'and')>Match ALL (AND)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="store_location" class="form-label">Store Location</label>
                    <input type="text" name="store_location" id="store_location" class="form-control"
                           value="{{ request('store_location') }}"
                           placeholder="e.g. Aisle 5">
                </div>
            </div>

        </div>
        <div class="card-footer bg-light text-end">
            <a href="{{ route('books.list') }}" class="btn btn-secondary">Reset All Filters</a>
            <button type="submit" class="btn btn-primary">Filter / Search</button>
        </div>
    </div>
</form>


@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new TomSelect("#categories", {
            plugins: ['remove_button'],

            valueField: 'id',
            labelField: 'text',
            searchField: 'text',

            load: function(query, callback) {
                if (!query.length) return callback();

                fetch(`/api/categories/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        callback(data);
                    }).catch(() => {
                        callback();
                    });
            },
            create: false
        });
    });
</script>
@endpush
