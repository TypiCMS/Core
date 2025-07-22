<div class="header-toolbar btn-toolbar">
    <button class="btn btn-sm btn-primary" value="true" id="exit" name="exit" type="submit">
        @lang('Save and exit')
    </button>
    <button class="btn btn-sm btn-light" type="submit">
        @lang('Save')
    </button>
    @if ($model->getTable() === 'pages' || Route::has(config('typicms.content_locale') . '::' . Str::singular($model->getTable())))
        @foreach (locales() as $locale)
            <a class="btn btn-sm btn-light btn-preview" href="{{ $model->previewUrl($locale) }}?preview=true" data-language="{{ $locale }}">
                @lang('Preview')
            </a>
        @endforeach
    @endif

    {{ $slot }}
    @if (!isset($langSwitcher) || $langSwitcher)
        @include('core::admin._lang-switcher-for-form')
    @endif
</div>
