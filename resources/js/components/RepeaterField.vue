<template>
    <div>
        <div
            v-if="['text', 'url', 'tel', 'email', 'date', 'month', 'week', 'time', 'datetime-local', 'number', 'range', 'color'].includes(type)"
            :class="{ 'form-group-translation': locale !== null }"
            class="mb-3"
        >
            <label :for="fieldId" class="form-label">{{ fieldLabel }}</label>
            <input
                :id="fieldId"
                :class="{ 'is-invalid': errors.length > 0 }"
                :data-language="locale"
                :max="max"
                :min="min"
                :name="fieldNameComplete"
                :placeholder="placeholder"
                :required="required"
                :step="step"
                :type="type"
                :value="initModel"
                class="form-control"
                @input="$emit('input', $event.target.value)"
            />
            <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
        </div>
        <div v-if="type === 'textarea'" :class="{ 'form-group-translation': locale !== null }" class="mb-3">
            <label :for="fieldId" class="form-label">{{ fieldLabel }}</label>
            <textarea
                :id="fieldId"
                :class="{ 'is-invalid': errors.length > 0 }"
                :data-language="locale"
                :name="fieldNameComplete"
                :placeholder="placeholder"
                :required="required"
                :rows="rows"
                :value="initModel"
                class="form-control"
                @input="$emit('input', $event.target.value)"
            ></textarea>
            <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
        </div>
        <div v-if="type === 'select'" :class="{ 'form-group-translation': locale !== null }" class="mb-3">
            <label :for="fieldId" class="form-label">{{ fieldLabel }}</label>
            <select
                :id="fieldId"
                :class="{ 'is-invalid': errors.length > 0 }"
                :data-language="locale"
                :name="fieldNameComplete"
                :required="required"
                :value="initModel"
                class="form-select"
                @change="$emit('input', $event.target.value)"
            >
                <option v-for="(label, value) in items" :value="value">{{ label }}</option>
            </select>
            <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
        </div>
        <div v-if="type === 'checkbox'" :class="{ 'form-group-translation': locale !== null }" class="form-check mb-3">
            <p class="form-label">{{ fieldLabel }}</p>
            <div class="form-check">
                <label :for="fieldId" class="form-check-label">{{ fieldLabel }}</label>
                <input :name="fieldNameComplete" type="hidden" value="0" />
                <input
                    :id="fieldId"
                    :checked="parseInt(initModel) === 1"
                    :class="{ 'is-invalid': errors.length > 0 }"
                    :data-language="locale"
                    :name="fieldNameComplete"
                    :required="required"
                    :type="type"
                    :value="1"
                    class="form-check-input"
                    @change="$emit('input', $event.target.checked ? 1 : 0)"
                />
                <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
            </div>
        </div>
        <div v-if="type === 'radio'" :class="{ 'form-group-translation': locale !== null }" class="mb-3">
            <p class="form-label">{{ fieldLabel }}</p>
            <input :name="fieldNameComplete" type="hidden" value="" />
            <div v-for="(label, value) in items" class="form-check">
                <label :for="fieldId + '_' + value" class="form-check-label">{{ $t(label) }}</label>
                <input
                    :id="fieldId + '_' + value"
                    :checked="initModel === value"
                    :class="{ 'is-invalid': errors.length > 0 }"
                    :data-language="locale"
                    :name="fieldNameComplete"
                    :required="required"
                    :type="type"
                    :value="value"
                    class="form-check-input"
                    @change="$emit('input', value)"
                />
                <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
            </div>
        </div>
        <div v-if="type === 'image'" :class="{ 'form-group-translation': locale !== null }" class="mb-3">
            <repeater-file-field :field="fieldNameComplete" :init-model="JSON.parse(initModel)" :label="fieldLabel" type="image"></repeater-file-field>
        </div>
    </div>
</template>

<script>
import FileManager from './FileManager.vue';
import RepeaterFileField from './RepeaterFileField.vue';

export default {
    components: { FileManager, RepeaterFileField },
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
            required: this.field.required,
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
