<div class="btn-group dropdown">
    <button class="btn btn-default dropdown-toggle" ng-class="{disabled: !checked.models.length}" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        Actions
        <span class="caret"></span>
        <span class="fa fa-spinner fa-spin fa-fw" ng-show="loading"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        <li><a ng-click="setItems('status', 1, 'online')" href="#">Online</a></li>
        <li><a ng-click="setItems('status', 0, 'offline')" href="#">Offline</a></li>
        <li role="separator" class="divider"></li>
        <li><a ng-click="deleteChecked()" href="#">Delete</a></li>
        <li role="separator" class="divider"></li>
        <li class="disabled"><a href="#">@{{ checked.models.length }} items selected</a></li>
    </ul>
</div>
