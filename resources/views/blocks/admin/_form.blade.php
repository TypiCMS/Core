<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Blocks')])
    @include('core::admin._title', ['default' => __('New block')])
    @component('core::admin._buttons-form', ['model' => $model])
    @endcomponent
</div>

<file-manager related-table="{{ $model->getTable() }}" :related-id="{{ $model->id ?? 0 }}"></file-manager>

<div class="content">
    @include('core::admin._form-errors')

    @if ($model->id)
        {!! BootForm::hidden('name') !!}
    @else
        {!! BootForm::text(__('Name'), 'name')->required()->autocomplete('off') !!}
    @endif

    <div class="mb-3">
        {!! TranslatableBootForm::hidden('status')->value(0) !!}
        {!! TranslatableBootForm::checkbox(__('Published'), 'status') !!}
    </div>
    <x-core::tiptap-editors :model="$model" name="body" :label="__('Body')" />
</div>
