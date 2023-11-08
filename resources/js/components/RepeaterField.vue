<template>
    <div :class="{ 'form-group-translation': locale !== null, 'form-check': type === 'checkbox' }" class="mb-3">
        <label v-if="type !== 'radio'" :class="type === 'checkbox' ? 'form-check-label' : 'form-label'" :for="fieldId">{{ fieldLabel }}</label>
        <input
            v-if="type === 'number' || type === 'text' || type === 'email' || type === 'date' || type === 'time' || type === 'datetime-local' || type === 'url'"
            :id="fieldId"
            :class="{ 'is-invalid': errors.length > 0 }"
            :data-language="locale"
            :max="max"
            :min="min"
            :name="fieldNameComplete"
            :placeholder="placeholder"
            :step="step"
            :type="type"
            :value="initModel"
            class="form-control"
            @input="$emit('input', $event.target.value)"
        />
        <input v-if="type === 'checkbox'" :class="{ 'is-invalid': errors.length > 0 }" :data-language="locale" :name="fieldNameComplete" type="hidden" value="0" />
        <input
            v-if="type === 'checkbox'"
            :id="fieldId"
            :checked="parseInt(initModel) === 1"
            :class="{ 'is-invalid': errors.length > 0 }"
            :data-language="locale"
            :name="fieldNameComplete"
            :type="type"
            :value="1"
            class="form-check-input"
            @change="$emit('input', $event.target.checked)"
        />
        <textarea
            v-if="type === 'textarea'"
            :id="fieldId"
            :class="{ 'is-invalid': errors.length > 0 }"
            :data-language="locale"
            :name="fieldNameComplete"
            :placeholder="placeholder"
            :rows="rows"
            :value="initModel"
            class="form-control"
            @input="$emit('input', $event.target.value)"
        ></textarea>
        <select
            v-if="type === 'select'"
            :id="fieldId"
            :class="{ 'is-invalid': errors.length > 0 }"
            :data-language="locale"
            :name="fieldNameComplete"
            :value="initModel"
            class="form-select"
            @change="$emit('input', $event.target.value)"
        >
            <option v-for="(item, key) in items" :value="key">{{ item }}</option>
        </select>
        <div v-if="type === 'radio'">
            <div v-for="(item, key) in items" class="form-check">
                <label :for="fieldId + '_' + key" class="form-check-label">{{ $t(item) }}</label>
                <input
                    :id="fieldId + '_' + key"
                    :checked="parseInt(initModel) === 1"
                    :class="{ 'is-invalid': errors.length > 0 }"
                    :data-language="locale"
                    :name="fieldNameComplete"
                    :type="type"
                    :value="key"
                    class="form-check-input"
                    @change="$emit('change', $event.target.checked)"
                />
            </div>
        </div>
        <div v-if="errors.length > 0" class="invalid-feedback">
            {{ errors[0] }}
        </div>
    </div>
</template>

<script>
export default {
    props: {
        field: {
            type: Object,
            required: true,
        },
        fieldName: {
            type: String,
            required: true,
        },
        index: {
            type: Number,
        },
        initModel: {
            required: true,
        },
        locale: {
            type: String,
            default: null,
        },
        errors: {
            type: Array,
            required: true,
        },
    },
    // created() {
    //     console.log(this.errors);
    // },
    data() {
        return {
            name: this.field.name,
            title: this.field.title,
            type: this.field.type,
            rows: this.field.rows,
            min: this.field.min,
            max: this.field.max,
            step: this.field.step,
            items: this.field.items,
            placeholder: this.field.placeholder,
        };
    },
    computed: {
        fieldNameComplete: function () {
            let fieldName = this.fieldName + '[' + this.index + '][' + this.name + ']';
            if (this.locale !== null) {
                fieldName += '[' + this.locale + ']';
            }
            return fieldName;
        },
        fieldId: function () {
            let id = this.fieldName + '_' + this.index + '_' + this.name;
            if (this.locale !== null) {
                id += '_' + this.locale;
            }
            return id;
        },
        fieldLabel: function () {
            let label = this.$i18n.t(this.title);
            if (this.locale !== null) {
                label += ' (' + this.locale + ')';
            }
            return label;
        },
    },
};
</script>
