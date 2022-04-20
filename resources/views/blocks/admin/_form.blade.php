@push('js')
    <script src="{{ asset('components/ckeditor4/ckeditor.js') }}"></script>
    <script src="{{ asset('components/ckeditor4/config-full.js') }}"></script>
@endpush

<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Blocks')])
    @include('core::admin._title', ['default' => __('New block')])
    @component('core::admin._buttons-form', ['model' => $model])
    @endcomponent
</div>

<file-manager related-table="{{ $model->getTable() }}" :related-id="{{ $model->id ?? 0 }}"></file-manager>

<div class="content">

    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}

    @if ($model->id)
    {!! BootForm::hidden('name') !!}
    @else
    {!! BootForm::text(__('Name'), 'name')->required() !!}
    @endif

    <div class="mb-3">
        {!! TranslatableBootForm::hidden('status')->value(0) !!}
        {!! TranslatableBootForm::checkbox(__('Published'), 'status') !!}
    </div>
    {!! TranslatableBootForm::textarea(__('Body'), 'body')->addClass('ckeditor-full') !!}

    </div>

</div>
