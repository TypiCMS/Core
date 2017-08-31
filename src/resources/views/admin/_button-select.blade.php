<div class="btn-group">
    <span class="btn btn-default" ng-class="{disabled: !displayedModels.length}">
        <input type="checkbox" ng-checked="allChecked && displayedModels.length && checked.models.length === displayedModels.length" ng-model="allChecked" ng-click="toggleCheckAll(allChecked)">
    </span>
    <button type="button" class="btn btn-default dropdown-toggle" ng-class="{disabled: !displayedModels.length}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">{{ __('Toggle Dropdown') }}</span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        <li><a ng-click="checkAll()" href="#">{{ __('All') }}</a></li>
        <li><a ng-click="uncheckAll()" href="#">{{ __('None') }}</a></li>
        <li role="separator" class="divider"></li>
        <li><a ng-click="check('status', {[TypiCMS.content_locale]: '1'})" href="#">{{ __('Published items') }}</a></li>
        <li><a ng-click="check('status', {[TypiCMS.content_locale]: '0'})" href="#">{{ __('Unpublished items') }}</a></li>
    </ul>
</div>
