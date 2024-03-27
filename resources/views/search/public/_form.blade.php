@if (Route::has($lang . '::search'))
    <form method="get" action="{{ route($lang . '::search') }}" class="search-form">
        <div class="input-group">
            <input class="search-input form-control" type="text" name="search" id="search" aria-label="@lang('Search')" placeholder="@lang('Search')" value="{{ old('search') }}" />
            <button class="search-button btn btn-primary" type="submit">Search</button>
        </div>
    </form>
@endif
