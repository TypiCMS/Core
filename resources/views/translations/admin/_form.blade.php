<div class="header">
    @include('core::admin._button-back', ['url' => $model->indexUrl(), 'title' => __('Translations')])
    @include('core::admin._title', ['default' => __('New translation')])
    @component('core::admin._buttons-form', ['model' => $model, 'langSwitcher' => false])
    @endcomponent
</div>

<div class="content">
    @include('core::admin._form-errors')

    @if (empty($model->id))
        {!! BootForm::text(__('Key'), 'key')->required() !!}
    @endif

    <p class="form-label">{{ __('Translations') }}</p>

    @foreach ($locales as $locale)
        <div class="mb-3">
            <div class="input-group">
                <span class="input-group-text">{{ strtoupper($locale) }}</span>
                {!! Form::text('translation[' . $locale . ']')->addClass('form-control')->addClass($errors->has('translation.' . $locale) ? 'is-invalid' : '') !!}
                {!! $errors->first('translation.' . $locale, '<div class="invalid-feedback">:message</div>') !!}
            </div>
        </div>
    @endforeach
</div>
