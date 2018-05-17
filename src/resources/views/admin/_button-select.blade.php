<div class="btn-group mr-2">
    <span class="btn btn-light" :class="{disabled: !filteredModels.length}">
        <input type="checkbox" :checked="allChecked && filteredModels.length && checkedModels.length === filteredModels.length" :model="allChecked" @click="toggleCheckAll(allChecked)">
    </span>
    <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" :class="{disabled: !filteredModels.length}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownSelect">
        <span class="caret"></span>
    </button>
    <ul class="dropdown-menu" aria-labelledby="dropdownSelect">
        <button type="button" class="dropdown-item" @click="checkAll()" href="#">{{ $t('All') }}</button>
        <button type="button" class="dropdown-item" @click="uncheckAll()" href="#">{{ $t('None') }}</button>
        <div class="dropdown-divider"></div>
        <button type="button" class="dropdown-item" @click="check('status', {[TypiCMS.content_locale]: '1'})" href="#">{{ $t('Published items') }}</button>
        <button type="button" class="dropdown-item" @click="check('status', {[TypiCMS.content_locale]: '0'})" href="#">{{ $t('Unpublished items') }}</button>
    </ul>
</div>
