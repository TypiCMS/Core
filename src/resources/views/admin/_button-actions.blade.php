<div class="btn-group">
    <button class="btn btn-light dropdown-toggle" ng-class="{disabled: !checked.models.length}" type="button" id="dropdownActions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        {{ __('Actions') }}
        <span class="caret"></span>
        <span class="fa fa-spinner fa-spin fa-fw" ng-show="loading"></span>
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownActions">
        @if (!isset($only) || in_array('online', $only) )
        <button type="button" class="dropdown-item" ng-click="setItems('status', {[TypiCMS.content_locale]: '1'}, 'publish')" href="#">{{ __('Publish') }}</button>
        @endif
        @if (!isset($only) || in_array('offline', $only) )
        <button type="button" class="dropdown-item" ng-click="setItems('status', {[TypiCMS.content_locale]: '0'}, 'unpublish')" href="#">{{ __('Unpublish') }}</button>
        <div class="dropdown-divider"></div>
        @endif
        @if (!isset($only) || in_array('delete', $only) )
        <button type="button" class="dropdown-item" ng-click="deleteChecked()" href="#">{{ __('Delete') }}</button>
        @endif
        <div role="separator" class="divider"></div>
        <button type="button" class="dropdown-item disabled" href="#">@{{ checked.models.length }} {{ __('items selected') }}</button>
    </div>
</div>
