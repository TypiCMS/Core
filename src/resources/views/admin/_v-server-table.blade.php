<div class="VueTables__loading" v-if="loading">
    <span class="VueTables__spinner fa fa-spin fa-fw fa-gear fa-3x"></span>
</div>
<v-server-table url="{{ route('api::index-news') }}" :columns="columns" :options="options"></v-server-table>
