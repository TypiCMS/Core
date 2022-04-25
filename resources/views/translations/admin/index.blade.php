@extends('core::admin.master')

@section('title', __('Translations'))

@section('content')

<item-list
    url-base="/api/translations"
    fields="id,key,translation"
    table="translations"
    title="translations"
    :publishable="false"
    :searchable="['key,translation']"
    :sorting="['key']">

    <template slot="add-button" v-if="$can('create translations')">
        @include('core::admin._button-create', ['module' => 'translations'])
    </template>

    <template slot="columns" slot-scope="{ sortArray }">
        <item-list-column-header name="checkbox" v-if="$can('update translations')||$can('delete translations')"></item-list-column-header>
        <item-list-column-header name="edit" v-if="$can('update translations')"></item-list-column-header>
        <item-list-column-header name="key" sortable :sort-array="sortArray" :label="$t('Key')"></item-list-column-header>
        <item-list-column-header name="translation_translated" sortable :sort-array="sortArray" :label="$t('Translation')"></item-list-column-header>
    </template>

    <template slot="table-row" slot-scope="{ model, checkedModels, loading }">
        <td class="checkbox" v-if="$can('update translations')||$can('delete translations')"><item-list-checkbox :model="model" :checked-models-prop="checkedModels" :loading="loading"></item-list-checkbox></td>
        <td v-if="$can('update translations')"><item-list-edit-button :url="'/admin/translations/'+model.id+'/edit'"></item-list-edit-button></td>
        <td>@{{ model.key }}</td>
        <td>@{{ model.translation_translated }}</td>
    </template>

</item-list>

@endsection
