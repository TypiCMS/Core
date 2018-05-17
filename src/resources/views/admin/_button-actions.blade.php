<div class="btn-group">
    <button class="btn btn-light dropdown-toggle" type="button" id="dropdownActions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        {{ __('Actions') }}
        <span class="caret"></span>
        <span class="fa fa-spinner fa-spin fa-fw"></span>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownActions">
        @if (!isset($only) || in_array('online', $only) )
        <button type="button" class="dropdown-item" @click="setItems('status', {[TypiCMS.content_locale]: '1'}, 'publish')">{{ __('Publish') }}</button>
        @endif
        @if (!isset($only) || in_array('offline', $only) )
        <button type="button" class="dropdown-item" @click="setItems('status', {[TypiCMS.content_locale]: '0'}, 'unpublish')">{{ __('Unpublish') }}</button>
        <div class="dropdown-divider"></div>
        @endif
        @if (!isset($only) || in_array('delete', $only) )
        <button type="button" class="dropdown-item" @click="deleteChecked()">{{ __('Delete') }}</button>
        @endif
        <div role="separator" class="divider"></div>
        <button type="button" class="dropdown-item disabled">@{{ checked.models.length }} {{ __('items selected') }}</button>
    </div>
</div>
