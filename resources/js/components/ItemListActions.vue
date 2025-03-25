<template>
    <div class="btn-group btn-group-sm">
        <button
            id="dropdownActions"
            :disabled="numberOfCheckedModels === 0 || loading"
            aria-expanded="true"
            aria-haspopup="true"
            class="btn btn-light dropdown-toggle"
            data-bs-toggle="dropdown"
            type="button"
        >
            {{ $t('Action') }}
        </button>
        <div aria-labelledby="dropdownActions" class="dropdown-menu">
            <button v-if="publishable" class="dropdown-item" type="button" @click="$emit('publish')">
                {{ $t('Publish') }}
                <span class="text-muted">({{ locale }})</span>
            </button>
            <button v-if="publishable" class="dropdown-item" type="button" @click="$emit('unpublish')">
                {{ $t('Unpublish') }}
                <span class="text-muted">({{ locale }})</span>
            </button>
            <div v-if="publishable" class="dropdown-divider"></div>
            <button v-if="deletable" class="dropdown-item" type="button" @click="$emit('destroy')">
                {{ $t('Delete') }}
            </button>
            <div class="divider" role="separator"></div>
            <button class="dropdown-item" disabled type="button">
                <small>{{
                    $t('# items selected', numberOfCheckedModels, {
                        count: numberOfCheckedModels,
                    })
                }}</small>
            </button>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        publishable: {
            type: Boolean,
            default: true,
        },
        deletable: {
            type: Boolean,
            default: true,
        },
        numberOfCheckedModels: {
            type: Number,
            required: true,
        },
        loading: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            locale: TypiCMS.content_locale,
        };
    },
};
</script>
