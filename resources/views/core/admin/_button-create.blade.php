<a class="btn btn-primary btn-sm header-btn-add" href="{{ $url ?? route('admin::create-' . Str::singular($module)) }}">
    <i class="icon-circle-plus text-white-50"></i>
    @lang('Create ' . Str::singular($module ?? 'item') )
</a>
