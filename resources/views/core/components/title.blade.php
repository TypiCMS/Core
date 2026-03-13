@props(['model', 'default' => null])

@if (empty($model->id))
    <h1 class="header-title">
        {{ $default ?? __('New') }}
    </h1>
@else
    <h1 class="header-title @if (!$model->presentTitle()) text-muted @endif">
        {{ $model->presentTitle() ?: __('Untitled') }}
    </h1>
@endif
