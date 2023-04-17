<a class="btn btn-primary btn-sm header-btn-add me-2 d-flex align-items-center" href="{{ $url ?? route('admin::create-'.Str::singular($module)) }}">
    <i class="bi bi-plus-circle-fill text-white-50 me-1"></i> @lang('Add')
</a>
