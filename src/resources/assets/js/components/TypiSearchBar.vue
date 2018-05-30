<template>
    <form class="search" @submit.prevent="search">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">
                    <span class="fa fa-search fa-fw"></span>
                </span>
            </div>
            <input type="text" class="form-control" v-model="string" :placeholder="$t('Search')" :aria-label="$t('Search')">
            <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit">{{ $t('Search') }}</button>
            </div>
        </div>
    </form>
</template>

<script>
export default {
    data(){
        return {
            string: '',
        }
    },
    methods: {
        search() {
            this.$emit('search', this.string);
        },
    },
};
</script>
