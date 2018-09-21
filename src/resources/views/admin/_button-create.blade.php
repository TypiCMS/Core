@can ('create-'.str_singular($module))
<a class="btn btn-primary btn-sm header-btn-add" href="{{ $url ?? route('admin::create-'.str_singular($module)) }}">
    <i class="fa fa-plus text-white-50"></i> @lang('Add')
</a>
@endcan
