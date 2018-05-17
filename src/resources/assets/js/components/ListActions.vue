<template>
    <div class="btn-group">
        <button class="btn btn-light dropdown-toggle" :disabled="numberOfCheckedModels === 0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" id="dropdownActions">
            {{ $t('Actions') }}
            <span class="caret"></span>
            <span class="fa fa-spinner fa-spin fa-fw" v-if="loading"></span>
        </button>
        <div class="dropdown-menu" aria-labelledby="dropdownActions">
            <button type="button" class="dropdown-item" @click="publish">{{ $t('Publish') }}</button>
            <button type="button" class="dropdown-item" @click="unpublish">{{ $t('Unpublish') }}</button>
            <div class="dropdown-divider"></div>
            <button type="button" class="dropdown-item" @click="destroy">{{ $t('Delete') }}</button>
            <div role="separator" class="divider"></div>
            <button type="button" class="dropdown-item" disabled>{{ numberOfCheckedModels }} {{ $t('items selected') }}</button>
        </div>
    </div>
</template>
<script>
    export default {
        props: {
            numberOfCheckedModels: {
                type: Number,
                required: true
            },
            loading: {
                type: Boolean,
                required: true
            },
        },
        methods: {
            destroy() {
                this.$emit('destroy');
            },
            publish() {
                this.$emit('publish');
            },
            unpublish() {
                this.$emit('unpublish');
            },
        }
    };
</script>
