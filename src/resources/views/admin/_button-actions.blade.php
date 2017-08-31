<div class="btn-group dropdown">
    <button class="btn btn-default dropdown-toggle" ng-class="{disabled: !checked.models.length}" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        {{ __('Actions') }}
        <span class="caret"></span>
        <span class="fa fa-spinner fa-spin fa-fw" ng-show="loading"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        @if (!isset($only) || in_array('online', $only) )
        <li><a ng-click="setItems('status', {[TypiCMS.content_locale]: '1'}, 'publish')" href="#">{{ __('Publish') }}</a></li>
        @endif
        @if (!isset($only) || in_array('offline', $only) )
        <li><a ng-click="setItems('status', {[TypiCMS.content_locale]: '0'}, 'unpublish')" href="#">{{ __('Unpublish') }}</a></li>
        <li role="separator" class="divider"></li>
        @endif
        @if (!isset($only) || in_array('delete', $only) )
        <li><a ng-click="deleteChecked()" href="#">{{ __('Delete') }}</a></li>
        @endif
        <li role="separator" class="divider"></li>
        <li class="disabled"><a href="#">@{{ checked.models.length }} {{ __('items selected') }}</a></li>
    </ul>
</div>
