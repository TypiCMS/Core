@can ($permission ?? 'update-'.Str::singular($module))
<a class="btn btn-light btn-xs" :href="'{{ $segment ?? $module }}/'+model.id+'/edit'">@lang('Edit')</a>
@endcan
