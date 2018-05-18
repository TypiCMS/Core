<template>
    <div class="btn-group mr-2">
        <span class="btn btn-light" :class="{disabled: !filteredModels.length}">
            <input type="checkbox" :checked="allChecked" :model="allChecked" @click="$emit('toggle')">
        </span>
        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" :class="{disabled: !filteredModels.length}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownSelect">
            <span class="caret"></span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownSelect">
            <button type="button" class="dropdown-item" @click="$emit('check-all')">{{ $t('All') }}</button>
            <button type="button" class="dropdown-item" @click="$emit('check-none')">{{ $t('None') }}</button>
            <div class="dropdown-divider"></div>
            <button type="button" class="dropdown-item" @click="$emit('check-published')">{{ $t('Published items') }}</button>
            <button type="button" class="dropdown-item" @click="$emit('check-unpublished')">{{ $t('Unpublished items') }}</button>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            filteredModels: {
                type: Array,
                required: true
            },
            allChecked: {
                type: Boolean,
                required: true
            },
        },
    };
</script>
