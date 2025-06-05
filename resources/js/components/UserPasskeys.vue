<template>
    <div>
        <h1>{{ t('Passkeys') }}</h1>
        <div class="mt-2">
            <form @submit.prevent="validatePasskeyProperties" class="flex items-center space-x-2">
                <div class="input-group">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        {{ t('passkeys::passkeys.name') }}
                    </label>
                    <input v-model="name" type="text" id="name" autocomplete="off" class="form-control" />
                    <span v-if="error" class="text-red-500 text-sm">{{ error }}</span>
                </div>

                <button type="submit" class="btn btn-outline-secondary btn-slug">
                    {{ t('passkeys::passkeys.create') }}
                </button>
            </form>
        </div>

        <div class="mt-6">
            <ul class="space-y-4">
                <li v-for="passkey in passkeys" :key="passkey.id" class="flex justify-between items-center p-4 bg-gray-100 rounded-lg shadow-sm">
                    <div class="text-gray-700">{{ passkey.name }}</div>
                    <div class="ml-2">
                        {{ t('passkeys::passkeys.last_used') }}:
                        {{ passkey.last_used_at ? passkey.last_used_at : t('passkeys::passkeys.not_used_yet') }}
                    </div>
                    <div>
                        <button @click="deletePasskey(passkey.id)" class="inline-flex justify-center py-2 px-4 text-sm font-medium text-white bg-red-600">
                            {{ t('passkeys::passkeys.delete') }}
                        </button>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    passkeys: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['create-passkey', 'delete-passkey']);

const name = ref('');
const error = ref(null);

const validatePasskeyProperties = () => {
    if (!name.value) {
        error.value = t('passkeys::passkeys.name_required');
        return;
    }
    error.value = null;
    emit('create-passkey', name.value);
    name.value = '';
};

const deletePasskey = (id) => {
    emit('delete-passkey', id);
};
</script>

<style scoped>
/* Ajoutez vos styles ici si nécessaire */
</style>
