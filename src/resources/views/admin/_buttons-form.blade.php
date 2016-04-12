<div class="btn-toolbar">
    <button class="btn-primary btn" value="true" id="exit" name="exit" type="submit">@lang('validation.attributes.save and exit')</button>
    <button class="btn-default btn" type="submit">@lang('validation.attributes.save')</button>
    @if ($model->getTable() == 'pages' || TypiCMS::getPageLinkedToModule($model->getTable()))
    <a class="btn btn-default btn-preview" href="{{ $model->previewUri() }}?preview=true">@lang('validation.attributes.preview')</a>
    @endif
    @include('core::admin._lang-switcher', ['js' => true])
</div>
