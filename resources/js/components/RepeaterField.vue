<template>
    <div>
        <input v-if="type === 'hidden'" :id="fieldId" :name="fieldNameComplete" :type="type" :value="modelValue" />
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
                :value="modelValue"
                :readonly="readonly"
                :disabled="disabled"
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
                :value="modelValue"
                :readonly="readonly"
                :disabled="disabled"
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
                :value="modelValue"
                :disabled="disabled"
                class="form-select"
                @change="$emit('input', $event.target.value)"
            >
                <option v-for="(label, value) in items" :value="value">{{ label }}</option>
            </select>
            <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
        </div>
        <div v-if="type === 'checkbox'" :class="{ 'form-group-translation': locale !== null }" class="mb-3">
            <p class="form-label">{{ fieldLabel }}</p>
            <div class="form-check">
                <label :for="fieldId" class="form-check-label">{{ fieldLabel }}</label>
                <input :name="fieldNameComplete" type="hidden" value="0" />
                <input
                    :id="fieldId"
                    :checked="+modelValue === 1"
                    :class="{ 'is-invalid': errors.length > 0 }"
                    :data-language="locale"
                    :name="fieldNameComplete"
                    :required="required"
                    :type="type"
                    :value="1"
                    :disabled="disabled"
                    class="form-check-input"
                    @change="$emit('input', $event.target.checked ? 1 : 0)"
                />
                <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
            </div>
        </div>
        <div v-if="type === 'radio'" :class="{ 'form-group-translation': locale !== null }" class="mb-3">
            <p class="form-label">{{ fieldLabel }}</p>
            <input :name="fieldNameComplete" type="hidden" value="" />
            <div v-for="(label, radioButtonValue) in items" class="form-check">
                <label :for="fieldId + '_' + radioButtonValue" class="form-check-label">{{ $t(label) }}</label>
                <input
                    :id="fieldId + '_' + radioButtonValue"
                    :checked="modelValue === radioButtonValue"
                    :class="{ 'is-invalid': errors.length > 0 }"
                    :data-language="locale"
                    :name="fieldNameComplete"
                    :required="required"
                    :type="type"
                    :value="radioButtonValue"
                    :disabled="disabled"
                    class="form-check-input"
                    @change="$emit('input', radioButtonValue)"
                />
                <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
            </div>
        </div>
        <div v-if="['image', 'document'].includes(type)" :class="{ 'form-group-translation': locale !== null }" class="mb-3">
            <p class="form-label">{{ fieldLabel }}</p>
            <input v-if="modelValue === null" :name="fieldNameComplete" :value="null" type="hidden" />
            <input v-if="modelValue !== null" :name="fieldNameComplete + '[id]'" :value="modelValue.id" type="hidden" />
            <input v-if="modelValue !== null" :name="fieldNameComplete + '[path]'" :value="modelValue.path" type="hidden" />
            <input v-if="modelValue !== null" :name="fieldNameComplete + '[extension]'" :value="modelValue.extension" type="hidden" />
            <input v-if="modelValue !== null" :name="fieldNameComplete + '[width]'" :value="modelValue.width" type="hidden" />
            <input v-if="modelValue !== null" :name="fieldNameComplete + '[height]'" :value="modelValue.height" type="hidden" />
            <input v-if="modelValue !== null" :name="fieldNameComplete + '[type]'" :value="modelValue.type" type="hidden" />
            <input v-if="modelValue !== null" :name="fieldNameComplete + '[name]'" :value="modelValue.name" type="hidden" />
            <input v-if="modelValue !== null" :name="fieldNameComplete + '[thumb_sm]'" :value="modelValue.thumb_sm" type="hidden" />
            <div :data-language="locale">
                <div v-if="modelValue !== null" class="filemanager-item filemanager-item-with-name filemanager-item-removable">
                    <div class="filemanager-item-wrapper">
                        <button class="filemanager-item-removable-button" type="button" @click="remove">
                            <i class="bi bi-x fs-5"></i>
                        </button>
                        <div v-if="modelValue.type === 'i'" class="filemanager-item-icon">
                            <div class="filemanager-item-image-wrapper">
                                <img :alt="modelValue.alt" :src="modelValue.thumb_sm" class="filemanager-item-image" />
                            </div>
                        </div>
                        <div v-else :class="'filemanager-item-icon-' + modelValue.type" class="filemanager-item-icon">
                            <i v-if="modelValue.type === 'a'" class="bi bi-file-earmark-music"></i>
                            <i v-if="modelValue.type === 'v'" class="bi bi-file-earmark-play"></i>
                            <i v-if="modelValue.type === 'd'" class="bi bi-file-earmark"></i>
                            <i v-if="modelValue.type === 'f'" class="bi bi-folder"></i>
                        </div>
                        <div class="filemanager-item-name">{{ modelValue.name }}</div>
                    </div>
                </div>
                <div v-if="modelValue === null" class="mb-3">
                    <button class="filemanager-field-btn-add" type="button" @click="openFilepicker" :disabled="disabled">
                        <i class="bi bi-plus-circle-fill text-white-50 me-1"></i>
                        {{ $t('Add') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FileManager from './FileManager.vue';

export default {
    components: { FileManager },
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
        modelValue: {
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
            disabled: this.field.disabled,
            readonly: this.field.readonly,
            placeholder: this.field.placeholder,
            choosingFile: false,
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
            let label = this.$t(this.title);
            if (this.locale !== null) {
                label += ' (' + this.locale + ')';
            }
            return label;
        },
    },
    mounted() {
        this.emitter.on('fileAdded', (file) => {
            if (this.choosingFile === true) {
                this.$emit('input', file);
            }
            this.choosingFile = false;
        });
    },
    methods: {
        remove() {
            this.$emit('input', null);
        },
        openFilepicker() {
            this.choosingFile = true;
            let options = {
                modal: true,
                modalIsInFront: true,
                multiple: false,
                open: true,
                overlay: true,
                single: true,
            };
            this.emitter.emit('openFilepicker', options);
        },
    },
};
</script>
