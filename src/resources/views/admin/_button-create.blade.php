<a class="btn btn-primary btn-sm header-btn-add me-2 d-flex align-items-center" href="{{ $url ?? route('admin::create-'.Str::singular($module)) }}">
    <svg class="text-white-50 me-2" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
    </svg> @lang('Add')
</a>
