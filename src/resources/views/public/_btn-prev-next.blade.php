    <div class="btn-group-prev-next">
        <div class="btn-group">
            <a class="btn btn-sm btn-prev btn-default @if(! $prev = $module::prev($model))disabled @endif" href="@if($prev){{ route($lang . '.' . $model->getTable() . '.slug', $prev->slug) }}@endif">@lang('core::global.Previous')</a>
            <a class="btn btn-sm btn-next btn-default @if(! $next = $module::next($model))disabled @endif" href="@if($next){{ route($lang . '.' . $model->getTable() . '.slug', $next->slug) }}@endif">@lang('core::global.Next')</a>
        </div>
    </div>
