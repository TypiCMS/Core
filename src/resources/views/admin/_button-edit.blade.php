@can ($permission ?? 'update-'.str_singular($module))
<a class="btn btn-default btn-xs" href="{{ $module }}/@{{ model.id }}/edit">@lang('Edit')</a>
@endcan
