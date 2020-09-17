<div class="items-navigator">
    <a class="items-navigator-back" href="{{ url($page->uri($lang)) }}">
        ← @lang('Back to '.$model->getTable().' list')
    </a>
    <div class="items-navigator-previous-next">
        <a class="items-navigator-previous @if (!$prev = $module::prev($model))disabled @endif"
            href="@if ($prev){{ route($lang.'::'.Str::singular($model->getTable()), $prev->slug) }}@endif">
            ← @lang('Previous')
        </a>
        <a class="items-navigator-next @if (!$next = $module::next($model))disabled @endif"
            href="@if ($next){{ route($lang.'::'.Str::singular($model->getTable()), $next->slug) }}@endif">
            @lang('Next') →
        </a>
    </div>
</div>
