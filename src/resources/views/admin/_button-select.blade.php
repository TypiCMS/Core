<div class="btn-group mr-2">
    <span class="btn btn-light" ng-class="{disabled: !displayedModels.length}">
        <input type="checkbox" ng-checked="allChecked && displayedModels.length && checked.models.length === displayedModels.length" ng-model="allChecked" ng-click="toggleCheckAll(allChecked)">
    </span>
    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" ng-class="{disabled: !displayedModels.length}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="selectMenu">
        <span class="caret"></span>
    </button>
    <div class="dropdown-menu" aria-labelledby="selectMenu">
        <button type="button" class="dropdown-item" ng-click="checkAll()" href="#">{{ __('All') }}</button>
        <button type="button" class="dropdown-item" ng-click="uncheckAll()" href="#">{{ __('None') }}</button>
        <div class="dropdown-divider"></div>
        <button type="button" class="dropdown-item" ng-click="check('status', {[TypiCMS.content_locale]: '1'})" href="#">{{ __('Published items') }}</button>
        <button type="button" class="dropdown-item" ng-click="check('status', {[TypiCMS.content_locale]: '0'})" href="#">{{ __('Unpublished items') }}</button>
    </div>
</div>
