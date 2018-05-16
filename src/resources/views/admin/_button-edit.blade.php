@can ($permission ?? 'update-'.str_singular($module))
<a class="btn btn-light btn-xs" :href="'{{ $module }}/'+model.id+'/edit'">@lang('Edit')</a>
@endcan
