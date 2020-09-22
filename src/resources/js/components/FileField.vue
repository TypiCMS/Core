<template>
    <div class="mb-4">
        <div class="form-group">
            <label :for="field">
                <span v-if="label"> {{ label }} </span>
                <span v-else>
                    {{ type === 'document' ? $t('Document') : $t('Image') }}
                </span>
            </label>
            <input type="hidden" :name="field" :id="field" :rel="field" v-model="id" />
            <div>
                <div v-if="id !== null" class="filemanager-item-removable">
                    <button class="filemanager-item-removable-button" type="button" @click="unsetData">
                        <svg
                            width="12"
                            height="12"
                            viewBox="0 0 1792 1792"
                            fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <path
                                d="M1490 1322q0 40-28 68l-136 136q-28 28-68 28t-68-28l-294-294-294 294q-28 28-68 28t-68-28l-136-136q-28-28-28-68t28-68l294-294-294-294q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 294 294-294q28-28 68-28t68 28l136 136q28 28 28 68t-28 68l-294 294 294 294q28 28 28 68z"
                            />
                        </svg>
                    </button>
                    <div v-if="type === 'document'"><span class="fa fa-fw fa-2x fa-file-o"></span> {{ name }}</div>
                    <div class="filemanager-item-image-wrapper" v-if="type === 'image'">
                        <img class="filemanager-item-image" :src="src" :alt="alt" />
                    </div>
                </div>
            </div>
            <div>
                <button v-if="id === null" @click="openFilepicker" class="btn btn-sm btn-secondary" type="button">
                    <span class="fa fa-plus fa-fw text-white-50"></span> {{ $t('Add') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        field: {
            type: String,
            required: true,
        },
        label: {
            type: String,
        },
        type: {
            type: String,
            required: true,
            validator: function (value) {
                // The value must match one of these strings
                return ['image', 'document'].indexOf(value) !== -1;
            },
        },
        data: {
            type: String,
            required: true,
        },
    },
    data() {
        return {
            loading: false,
            choosingFile: false,
            id: null,
            name: null,
            src: null,
            alt: null,
        };
    },
    created() {
        if (this.data !== '') {
            this.setData(JSON.parse(this.data));
        }
    },
    mounted() {
        this.$root.$on('fileAdded', (file) => {
            if (this.choosingFile === true) {
                this.setData(file);
            }
            this.choosingFile = false;
        });
    },
    methods: {
        setData(file) {
            this.id = file.id;
            this.name = file.name;
            this.src = file.thumb_sm;
            this.alt = file.alt_attribute_translated;
        },
        unsetData() {
            this.id = null;
            this.name = null;
            this.src = null;
            this.alt = null;
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
