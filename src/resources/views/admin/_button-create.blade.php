@can('create-'.str_singular($module))
<a href="{{ route('admin::create-'.str_singular($module)) }}" class="btn-add" title="@lang($module.'::global.New')">
    <i class="fa fa-plus-circle"></i><span class="sr-only">@lang($module.'::global.New')</span>
</a>
@endcan
