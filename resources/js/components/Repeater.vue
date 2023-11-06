<template>
    <div class="mb-3">
        <input type="hidden" :name="name + '[]'" v-if="items.length === 0" />
        <label class="form-label">{{ $t(title) }}</label>
        <div>
            <button class="btn btn-secondary btn-sm" @click.prevent="add" :disabled="items.length >= maxItems">
                <span class="bi bi-plus-circle-fill text-white-50 me-1"></span>
                {{ $t('Add') }}
            </button>
        </div>

        <draggable class="d-flex flex-column gap-3 mt-3" v-model="items" group="items" handle=".handle" v-if="items.length > 0">
            <div class="d-flex gap-2 card flex-row p-3 bg-light" v-for="(item, index) in items">
                <button class="btn btn-light handle">
                    <i class="bi bi-grip-vertical"></i>
                </button>
                <div v-for="field in fields">
                    <label class="form-label" :for="field.name">{{ $t(field.title) }}</label>
                    <input
                        class="form-control"
                        v-if="field.type !== 'select'"
                        :type="field.type"
                        :id="field.name"
                        :name="name + '[' + index + '][' + field.name + ']'"
                        :placeholder="field.placeholder"
                        v-model="item[field.name]"
                    />
                    <select class="form-select" v-if="field.type === 'select'" :name="name + '[' + index + '][' + field.name + ']'" v-model="item[field.name]">
                        <option :value="key" v-for="(item, key) in field.items">{{ item }}</option>
                    </select>
                </div>
                <button class="btn btn-danger btn-sm" @click.prevent="remove(item)">{{ $t('Delete') }}</button>
            </div>
        </draggable>
    </div>
</template>

<script>
import draggable from 'vuedraggable';

export default {
    components: {
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
