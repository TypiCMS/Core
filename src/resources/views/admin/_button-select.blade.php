<div class="btn-group mr-2">
    <span class="btn btn-light" :class="{disabled: !filteredModels.length}">
        <input type="checkbox" :checked="allChecked && filteredModels.length && checkedModels.length === filteredModels.length" :model="allChecked" @click="toggleCheckAll(allChecked)">
    </span>
    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" :class="{disabled: !filteredModels.length}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
        <a class="dropdown-item" @click="checkAll()" href="#">{{ $t('All') }}</a>
        <a class="dropdown-item" @click="uncheckAll()" href="#">{{ $t('None') }}</a>
        <div class="dropdown-divider"></div>
        <a class="dropdown-item" @click="check('status', {[TypiCMS.content_locale]: '1'})" href="#">{{ $t('Published items') }}</a>
        <a class="dropdown-item" @click="check('status', {[TypiCMS.content_locale]: '0'})" href="#">{{ $t('Unpublished items') }}</a>
    </ul>
</div>
