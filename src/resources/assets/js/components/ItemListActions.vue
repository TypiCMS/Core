<template>
    <div class="btn-group btn-group-sm">
        <button class="btn btn-light dropdown-toggle" :disabled="numberOfCheckedModels === 0 || loading" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" id="dropdownActions">
            {{ $t('Action') }}
            <span class="caret"></span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownActions">
            <button type="button" class="dropdown-item" @click="$emit('publish')">{{ $t('Publish') }}</button>
            <button type="button" class="dropdown-item" @click="$emit('unpublish')">{{ $t('Unpublish') }}</button>
            <div class="dropdown-divider"></div>
            <button type="button" class="dropdown-item" @click="$emit('destroy')">{{ $t('Delete') }}</button>
            <div role="separator" class="divider"></div>
            <button type="button" class="dropdown-item" disabled><small>{{ $tc('# items selected', numberOfCheckedModels, { count: numberOfCheckedModels }) }}</small></button>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        numberOfCheckedModels: {
            type: Number,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
        },
    },
};
</script>
