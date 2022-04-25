@extends('core::admin.master')

@section('title', __('Terms'))

@section('content')

<item-list
    url-base="/api/taxonomies/{{ $taxonomy->id }}/terms"
    fields="id,taxonomy_id,title,position"
    table="terms"
    title="terms"
    :publishable="false"
    :exportable="false"
    :searchable="['title']"
    :sorting="['position']">

    <template slot="back-button">
        @include('core::admin._button-back', ['url' => route('admin::index-taxonomies'), 'title' => __('Taxonomies')])
    </template>

    <template slot="add-button">
        <span v-if="$can('create terms')">
            @include('core::admin._button-create', ['url' => route('admin::create-term', $taxonomy)])
        </span>
    </template>

    <template slot="columns" slot-scope="{ sortArray }">
        <item-list-column-header name="checkbox" v-if="$can('update terms')||$can('delete terms')"></item-list-column-header>
        <item-list-column-header name="edit" v-if="$can('update terms')"></item-list-column-header>
        <item-list-column-header name="position" sortable :sort-array="sortArray" :label="$t('Position')"></item-list-column-header>
        <item-list-column-header name="title_translated" sortable :sort-array="sortArray" :label="$t('Title')"></item-list-column-header>
    </template>

    <template slot="table-row" slot-scope="{ model, checkedModels, loading }">
        <td class="checkbox" v-if="$can('update terms')||$can('delete terms')"><item-list-checkbox :model="model" :checked-models-prop="checkedModels" :loading="loading"></item-list-checkbox></td>
        <td v-if="$can('update terms')"><item-list-edit-button :url="'/admin/taxonomies/'+model.taxonomy_id+'/terms/'+model.id+'/edit'"></item-list-edit-button></td>
        <td><item-list-position-input :model="model"></item-list-position-input></td>
        <td v-html="model.title_translated"></td>
    </template>

</item-list>

@endsection
