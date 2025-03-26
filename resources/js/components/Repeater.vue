<template>
    <div class="mb-3">
        <input v-if="items.length === 0" :name="name" type="hidden" />
        <label class="form-label">{{ $t(title) }}</label>

        <draggable v-if="items.length > 0" v-model="items" :group="'items_' + name" class="d-flex flex-column gap-3 mb-3" handle=".handle" @change="errors = []" item-key="index">
            <template #item="{ element, index }">
                <div class="d-flex gap-2 card item">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <i class="bi bi-arrows-move handle"></i>
                        <button class="btn btn-danger btn-sm" @click.prevent="remove(element)">{{ $t('Delete') }}</button>
                    </div>
                    <div class="card-body d-flex flex-row gap-2 justify-content-between flex-wrap">
                        <div v-for="field in fields" :class="[{ 'flex-grow-1': field.type !== 'hidden' }, field.class]">
                            <template v-if="field.translatable">
                                <repeater-field
                                    v-for="locale in locales"
                                    :key="'item_' + name + '_' + index + '_' + field.name + '_' + locale.short"
                                    v-model="element[field.name][locale.short]"
                                    :errors="getError(index, field.name, locale.short)"
                                    :field="field"
                                    :field-name="name"
                                    :index="index"
                                    :locale="locale.short"
                                ></repeater-field>
                            </template>
                            <repeater-field
                                v-else
                                :key="'item_' + name + '_' + index + '_' + field.name"
                                v-model="element[field.name]"
                                :errors="getError(index, field.name, null)"
                                :field="field"
                                :field-name="name"
                                :index="index"
                            ></repeater-field>
                        </div>
                    </div>
                </div>
            </template>
        </draggable>
        <div>
            <button :disabled="maxItems !== null && items.length >= maxItems" class="btn btn-secondary btn-sm" @click.prevent="add">
                <span class="bi bi-plus-circle-fill text-white-50 me-1"></span>
                {{ $t('Add') }}
            </button>
        </div>
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import RepeaterField from './RepeaterField.vue';

export default {
    components: {
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
        initErrors: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            locales: window.TypiCMS.locales,
            items: this.initItems || [],
            title: this.config.title,
            name: this.config.name,
            maxItems: this.config.max_items || null,
            fields: this.config.fields,
            errors: this.initErrors,
        };
    },
    methods: {
        add() {
            if (this.maxItems === null || this.items.length < this.maxItems) {
                this.items.push(this.emptyItem());
            }
        },
        emptyItem() {
            let item = {};
            this.fields.forEach((field) => {
                if (field.translatable) {
                    item[field.name] = {};
                    this.locales.forEach((locale) => {
                        item[field.name][locale.short] = field.default !== undefined ? field.default : null;
                    });
                } else {
                    item[field.name] = field.default !== undefined ? field.default : null;
                }
            });

            return item;
        },
        getError(index, fieldName, locale) {
            if (this.errors.length === 0) {
                return [];
            }
            if (locale !== null) {
                if (this.errors[index] === undefined) {
                    return [];
                }
                if (this.errors[index][fieldName] === undefined) {
                    return [];
                }
                return this.errors[index][fieldName][locale] ?? [];
            }
            if (this.errors[index] === undefined) {
                return [];
            }
            return this.errors[index][fieldName] ?? [];
        },
        remove(item) {
            const index = this.items.indexOf(item);
            this.items.splice(index, 1);
        },
    },
};
</script>
