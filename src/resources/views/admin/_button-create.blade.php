@can ('create-'.str_singular($module))
<a href="{{ route('admin::create-'.str_singular($module)) }}" class="btn-add" title="@lang('New '.str_singular($module))">
    <i class="fa fa-plus-circle"></i><span class="sr-only">@lang('New '.str_singular($module))</span>
</a>
@endcan
