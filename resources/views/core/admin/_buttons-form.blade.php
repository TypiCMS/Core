<div class="header-toolbar btn-toolbar">
    <button class="btn btn-sm btn-primary me-2" value="true" id="exit" name="exit" type="submit">{{ __('Save and exit') }}</button>
    <button class="btn btn-sm btn-secondary me-2" type="submit">{{ __('Save') }}</button>
    @if ($model->getTable() === 'pages' || Route::has($locale.'::'.Str::singular($model->getTable())))
    <a class="btn btn-sm btn-secondary btn-preview me-2" href="{{ $model->previewUri() }}?preview=true">{{ __('Preview') }}</a>
    @endif
    {{ $slot }}
    @if (!isset($langSwitcher) || $langSwitcher)
    @include('core::admin._lang-switcher-for-form')
    @endif
</div>
