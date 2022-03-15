@extends('core::admin.master')

@section('title', __('Tags'))

@section('content')

<item-list
    url-base="/api/tags"
    locale="{{ config('typicms.content_locale') }}"
    fields="id,tag,slug"
    table="tags"
    title="tags"
    :multilingual="false"
    :publishable="false"
    :searchable="['tag']"
    :sorting="['tag']">

    <template slot="add-button" v-if="$can('create tags')">
        @include('core::admin._button-create', ['module' => 'tags'])
    </template>

    <template slot="columns" slot-scope="{ sortArray }">
        <item-list-column-header name="checkbox" v-if="$can('update tags')||$can('delete tags')"></item-list-column-header>
        <item-list-column-header name="edit" v-if="$can('update tags')"></item-list-column-header>
        <item-list-column-header name="tag" sortable :sort-array="sortArray" :label="$t('Tag')"></item-list-column-header>
        <item-list-column-header name="uses" sortable :sort-array="sortArray" :label="$t('Uses')"></item-list-column-header>
    </template>

    <template slot="table-row" slot-scope="{ model, checkedModels, loading }">
        <td class="checkbox" v-if="$can('update tags')||$can('delete tags')"><item-list-checkbox :model="model" :checked-models-prop="checkedModels" :loading="loading"></item-list-checkbox></td>
        <td v-if="$can('update tags')">@include('core::admin._button-edit', ['module' => 'tags'])</td>
        <td>@{{ model.tag }}</td>
        <td>@{{ model.uses }}</td>
    </template>

</item-list>

@endsection
