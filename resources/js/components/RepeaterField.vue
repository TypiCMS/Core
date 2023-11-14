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
                :value="value"
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
                :value="value"
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
                :value="value"
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
                    :checked="parseInt(value) === 1"
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
            <div v-for="(label, radioButtonValue) in items" class="form-check">
                <label :for="fieldId + '_' + radioButtonValue" class="form-check-label">{{ $t(label) }}</label>
                <input
                    :id="fieldId + '_' + radioButtonValue"
                    :checked="value === radioButtonValue"
                    :class="{ 'is-invalid': errors.length > 0 }"
                    :data-language="locale"
                    :name="fieldNameComplete"
                    :required="required"
                    :type="type"
                    :value="radioButtonValue"
                    class="form-check-input"
                    @change="$emit('input', radioButtonValue)"
                />
                <div v-if="errors.length > 0" class="invalid-feedback">{{ errors[0] }}</div>
            </div>
        </div>
        <div v-if="type === 'image'" :class="{ 'form-group-translation': locale !== null }" class="mb-3">
            <div>
                <p class="form-label mb-2">
                    {{ type === 'document' ? $t('Document') : $t('Image') }}
                </p>
                <input :data-language="locale" :name="fieldNameComplete" :value="value" type="hidden" />
                <div>
                    <div v-if="file !== null" class="filemanager-item filemanager-item-with-name filemanager-item-removable">
                        <div class="filemanager-item-wrapper">
                            <button class="filemanager-item-removable-button" type="button" @click="remove">
                                <i class="bi bi-x fs-5"></i>
                            </button>
                            <div v-if="file.type === 'i'" class="filemanager-item-icon">
                                <div class="filemanager-item-image-wrapper">
                                    <img :alt="file.alt" :src="file.thumb_sm" class="filemanager-item-image" />
                                </div>
                            </div>
                            <div v-else :class="'filemanager-item-icon-' + file.type" class="filemanager-item-icon">
                                <i v-if="file.type === 'a'" class="bi bi-file-earmark-music"></i>
                                <i v-if="file.type === 'v'" class="bi bi-file-earmark-play"></i>
                                <i v-if="file.type === 'd'" class="bi bi-file-earmark"></i>
                                <i v-if="file.type === 'f'" class="bi bi-folder"></i>
                            </div>
                            <div class="filemanager-item-name">{{ file.name }}</div>
                        </div>
                    </div>
                </div>
                <div v-if="file === null" class="mb-3">
                    <button class="filemanager-field-btn-add" type="button" @click="openFilepicker">
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
        value: {
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
            choosingFile: false,
        };
    },
    computed: {
        file: function () {
            if (this.type === 'image') {
                return JSON.parse(this.value);
            }
            return null;
        },
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
    mounted() {
        this.$root.$on('fileAdded', (file) => {
            if (this.choosingFile === true) {
                this.file = file;
                this.$emit('input', JSON.stringify(file));
            }
            this.choosingFile = false;
        });
    },
    methods: {
        remove() {
            this.file = null;
            this.$emit('input', null);
        },
        openFilepicker() {
            this.choosingFile = true;
            let options = {
                open: true,
                multiple: false,
                overlay: true,
                single: true,
                modal: true,
            };
            this.$root.$emit('openFilepicker', options);
        },
    },
};
</script>
