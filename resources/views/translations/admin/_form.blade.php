<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Translations')])
    @include('core::admin._title', ['default' => __('New translation')])
    @component('core::admin._buttons-form', ['model' => $model, 'langSwitcher' => false])
    @endcomponent
</div>

<div class="content">

    @include('core::admin._form-errors')

    {!! BootForm::hidden('id') !!}

    @if ($model->id)
    {!! BootForm::hidden('key') !!}
    @else
    {!! BootForm::text(__('Key'), 'key')->required() !!}
    @endif

    <label class="form-label">{{ __('Translations') }}</label>

    @foreach ($locales as $lang)
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text">{{ strtoupper($lang) }}</span>
                {!! Form::text('translation['.$lang.']')->addClass('form-control')->addClass($errors->has('translation.'.$lang) ? 'is-invalid' : '') !!}
                {!! $errors->first('translation.'.$lang, '<div class="invalid-feedback">:message</div>') !!}
            </div>
        </div>
    @endforeach

</div>
