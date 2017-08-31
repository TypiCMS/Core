<div class="btn-toolbar">
    <button class="btn-primary btn" value="true" id="exit" name="exit" type="submit">{{ __('Save and exit') }}</button>
    <button class="btn-default btn" type="submit">{{ __('Save') }}</button>
    @if ($model->getTable() == 'pages' || TypiCMS::getPageLinkedToModule($model->getTable()))
    <a class="btn btn-default btn-preview" href="{{ $model->previewUri() }}?preview=true">{{ __('Preview') }}</a>
    @endif
    @if (method_exists($model, 'files'))
    <button class="btn btn-success" id="select-files" type="button" {!! $model->id ? '' : 'disabled title="'.__('Save this item first, then add files.').'"' !!}>{{ __('Add files') }}</button>
    @endif
    {{ $slot }}
    @include('core::admin._lang-switcher-for-form')
</div>
