<div class="btn-group">
    <span class="btn btn-default" ng-class="{disabled: !displayedModels.length}">
        <input type="checkbox" ng-checked="allChecked && displayedModels.length && checked.models.length === displayedModels.length" ng-model="allChecked" ng-click="toggleCheckAll(allChecked)">
    </span>
    <button type="button" class="btn btn-default dropdown-toggle" ng-class="{disabled: !displayedModels.length}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
        <span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        <li><a ng-click="checkAll()" href="#">All</a></li>
        <li><a ng-click="uncheckAll()" href="#">None</a></li>
        <li role="separator" class="divider"></li>
        <li><a ng-click="check('status', 1)" href="#">Online</a></li>
        <li><a ng-click="check('status', 0)" href="#">Offline</a></li>
    </ul>
</div>
