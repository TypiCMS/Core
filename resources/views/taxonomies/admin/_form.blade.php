<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Taxonomies')])
    @include('core::admin._title', ['default' => __('New taxonomy')])
    @component('core::admin._buttons-form', ['model' => $model])
    @endcomponent
</div>

<div class="content">

    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}

    {!! BootForm::text(__('Name'), 'name')->required() !!}
    {!! TranslatableBootForm::text(__('Info for search results'), 'result_string') !!}

    @include('core::form._title-and-slug')

    {!! BootForm::text(__('Validation rule'), 'validation_rule') !!}

    {!! Form::hidden('modules[]')->value('') !!}
    @if (!empty($modules))
    <label class="form-label">@lang('Use in modules')</label>
    @foreach ($modules as $module => $properties)
    <div class="form-check">
        {!! Form::checkbox('modules[]', $module)->id($module)->addClass('form-check-input') !!}
        <label class="form-check-label" for="{{ $module }}">@lang(ucfirst($module))</label>
    </div>
    @endforeach
    @endif

</div>
