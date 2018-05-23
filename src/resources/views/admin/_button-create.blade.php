@can ('create-'.str_singular($module))
<a href="{{ route('admin::create-'.str_singular($module)) }}" class="btn btn-primary mr-2">
    <i class="fa fa-plus text-white-50"></i> @lang('Add')
</a>
@endcan
