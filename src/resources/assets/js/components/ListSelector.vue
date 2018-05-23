<template>
    <div class="btn-group btn-group-list-selector">
        <div class="btn btn-light">
            <input type="checkbox" :disabled="!filteredModels.length" :checked="allChecked" :model="allChecked" @click="allChecked ? $emit('check-none') : $emit('check-all')">
        </div>
        <button type="button" class="btn btn-light dropdown-toggle dropdown-toggle-split" :disabled="!filteredModels.length" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" id="dropdownSelect">
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
            required: true,
        },
        allChecked: {
            type: Boolean,
            required: true,
        },
    },
};
</script>
