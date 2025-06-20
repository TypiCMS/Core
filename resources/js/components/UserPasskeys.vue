<template>
    <div>
        <h1>{{ t('Passkeys') }}</h1>
        <form class="mb-3" @submit.prevent="validatePasskeyProperties">
            <label for="name" class="form-label">
                {{ t('Name') }}
            </label>
            <div class="input-group" :class="{ 'is-invalid': error }">
                <input v-model="name" type="text" id="name" autocomplete="off" class="form-control" :class="{ 'is-invalid': error }" />
                <button type="submit" class="btn btn-outline-secondary btn-slug">
                    {{ t('Create') }}
                </button>
            </div>
            <span class="invalid-feedback" v-if="error">{{ error }}</span>
        </form>

        <div v-if="passkeys.length !== 0">
            <ul>
                <li v-for="passkey in passkeys" :key="passkey.id" class="flex justify-between items-center p-4 bg-gray-100 rounded-lg shadow-sm">
                    <div>{{ passkey.name }}</div>
                    <div>
                        {{ t('Last used') }}:
                        {{ passkey.last_used_at ? formatDateTime(passkey.last_used_at) : t('Not used yet') }}
                    </div>
                    <div>
                        <button class="btn btn-link text-danger btn-sm" type="button" @click="deletePasskey(passkey.id)">
                            {{ t('Delete') }}
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
import fetcher from '../admin/fetcher';

const { t } = useI18n();

const props = defineProps({
    passkeys: {
        type: Array,
        required: true,
    },
});

const name = ref('');
const error = ref(null);

const validatePasskeyProperties = () => {
    if (!name.value) {
        error.value = t('Name required');
        return;
    }
    error.value = null;
    name.value = '';

    addPassKey();
};

async function addPassKey() {
    const response = await fetcher('/api/passkeys/generate-options');
    const options = await response.json();
    const startAuthenticationResponse = await window.startRegistration(options);

    await fetcher('/api/passkeys', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            options: JSON.stringify(options),
            passkey: JSON.stringify(startAuthenticationResponse),
        }),
    });
}

async function deletePasskey(id) {
    if (!confirm(t('Are you sure you want to delete this passkey?'))) {
        return;
    }
    try {
        const response = await fetcher('/api/passkeys/' + id, {
            method: 'DELETE',
        });
        if (!response.ok) {
            const responseData = await response.json();
            throw new Error(responseData.message);
        }
        alertify.success(t('Item successfully deleted.'));
    } catch (error) {
        console.log(error);
        alertify.error(t(error.message) || t('Sorry, an error occurred.'));
    }
}
</script>
