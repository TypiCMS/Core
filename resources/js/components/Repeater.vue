<template>
    <div class="mb-3">
        <input v-if="items.length === 0" :name="name + '[]'" type="hidden" />
        <label class="form-label">{{ $t(title) }}</label>
        <div>
            <button :disabled="items.length >= maxItems" class="btn btn-secondary btn-sm" @click.prevent="add">
                <span class="bi bi-plus-circle-fill text-white-50 me-1"></span>
                {{ $t('Add') }}
            </button>
        </div>

        <draggable v-if="items.length > 0" v-model="items" class="d-flex flex-column gap-3 mt-3" group="items" handle=".handle">
            <div v-for="(item, index) in items" class="d-flex gap-2 card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <i class="bi bi-arrows-move handle"></i>
                    <button class="btn btn-danger btn-sm" @click.prevent="remove(item)">{{ $t('Delete') }}</button>
                </div>
                <div class="card-body d-flex flex-row gap-2 justify-content-between">
                    <div v-for="field in fields" class="flex-grow-1">
                        <repeater-translatable-field
                            v-for="locale in locales"
                            v-if="field.translatable"
                            :field="field"
                            :field-name="name"
                            :index="index"
                            :init-model="item[field.name] ? item[field.name][locale.short] : ''"
                            :locale="locale.short"
                        ></repeater-translatable-field>
                        <repeater-field v-else :field="field" :field-name="name" :index="index" :item="item"></repeater-field>
                    </div>
                </div>
            </div>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import RepeaterField from './RepeaterField.vue';
import RepeaterTranslatableField from './RepeaterTranslatableField.vue';

export default {
    components: {
        RepeaterTranslatableField,
        RepeaterField,
        draggable,
    },
    props: {
        initItems: {
            type: Array,
        },
        config: {
            type: Object,
            required: true,
        },
    },
    data() {
        return {
            locales: window.TypiCMS.locales,
            items: this.initItems || [],
            title: this.config.title,
            name: this.config.name,
            maxItems: this.config.max_items,
            fields: this.config.fields,
        };
    },
    created() {
        // console.log(this.config);
    },
    computed: {},
    methods: {
        add() {
            if (this.items.length >= this.max_items) {
                return;
            }
            this.items.push({});
        },
        remove(item) {
            var index = this.items.indexOf(item);
            this.items.splice(index, 1);
        },
    },
};
</script>
