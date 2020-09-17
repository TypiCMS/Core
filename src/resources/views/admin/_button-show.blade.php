@can ($permission ?? 'show-'.Str::singular($module))
<a class="btn btn-light btn-xs" :href="'{{ $segment ?? $module }}/'+model.id">@lang('Show')</a>
@endcan
