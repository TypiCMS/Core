<div class="btn-group-prev-next">
    <div class="btn-group">
        <a class="btn btn-sm btn-outline-secondary btn-prev @if (!$prev = $module::prev($model))disabled @endif" href="@if ($prev){{ route($lang.'::'.Illuminate\Support\Str::singular($model->getTable()), $prev->slug) }}@endif">@lang('Previous')</a>
        <a class="btn btn-sm btn-outline-secondary btn-list" href="{{ url($page->uri($lang)) }}">{{ $page->title }}</a>
        <a class="btn btn-sm btn-outline-secondary btn-next @if (!$next = $module::next($model))disabled @endif" href="@if ($next){{ route($lang.'::'.Illuminate\Support\Str::singular($model->getTable()), $next->slug) }}@endif">@lang('Next')</a>
    </div>
</div>
