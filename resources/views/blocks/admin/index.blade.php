@extends('core::admin.master')

@section('title', __('Content blocks'))

@section('content')

<item-list
    url-base="/api/blocks"
    locale="{{ config('typicms.content_locale') }}"
    fields="id,name,body,status,body"
    table="blocks"
    title="blocks"
    :searchable="['name,body']"
    :sorting="['name']">

    <template slot="add-button" v-if="$can('create blocks')">
        @include('core::admin._button-create', ['module' => 'blocks'])
    </template>

    <template slot="columns" slot-scope="{ sortArray }">
        <item-list-column-header name="checkbox" v-if="$can('update blocks')||$can('delete blocks')"></item-list-column-header>
        <item-list-column-header name="edit" v-if="$can('update blocks')"></item-list-column-header>
        <item-list-column-header name="status_translated" sortable :sort-array="sortArray" :label="$t('Status')"></item-list-column-header>
        <item-list-column-header name="name" sortable :sort-array="sortArray" :label="$t('Name')"></item-list-column-header>
        <item-list-column-header name="body_translated" sortable :sort-array="sortArray" :label="$t('Content')"></item-list-column-header>
    </template>

    <template slot="table-row" slot-scope="{ model, checkedModels, loading }">
        <td class="checkbox" v-if="$can('update blocks')||$can('delete blocks')"><item-list-checkbox :model="model" :checked-models-prop="checkedModels" :loading="loading"></item-list-checkbox></td>
        <td v-if="$can('update blocks')">@include('core::admin._button-edit', ['module' => 'blocks'])</td>
        <td><item-list-status-button :model="model"></item-list-status-button></td>
        <td><span class="badge bg-secondary">@{{ model.name }}</span></td>
        <td>@{{ model.body_translated }}</td>
    </template>

</item-list>

@endsection
