<div class="btn-group dropdown pull-right">
    <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
        Export
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
        <li><a ng-csv="models" filename="members-all.csv" href="#">All to CSV</a></li>
        <li><a ng-csv="displayedModels" filename="members.csv" href="#">@{{ displayedModels.length }} results to CSV</a></li>
    </ul>
</div>
