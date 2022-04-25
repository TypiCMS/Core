@extends('core::admin.master')

@section('title', __('Taxonomies'))

@section('content')

<item-list
    url-base="/api/taxonomies"
    fields="id,title,name,validation_rule,position,result_string,modules"
    table="taxonomies"
    title="taxonomies"
    :publishable="false"
    :exportable="false"
    :searchable="['title,name,validation_rule,result_string']"
    :sorting="['position']">

    <template slot="add-button" v-if="$can('create taxonomies')">
        @include('core::admin._button-create', ['module' => 'taxonomies'])
    </template>

    <template slot="columns" slot-scope="{ sortArray }">
        <item-list-column-header name="checkbox" v-if="$can('update taxonomies')||$can('delete taxonomies')"></item-list-column-header>
        <item-list-column-header name="edit" v-if="$can('update taxonomies')"></item-list-column-header>
        <item-list-column-header name="edit" v-if="$can('update terms')"></item-list-column-header>
        <item-list-column-header name="position" sortable :sort-array="sortArray" :label="$t('Position')"></item-list-column-header>
        <item-list-column-header name="name" sortable :sort-array="sortArray" :label="$t('Name')"></item-list-column-header>
        <item-list-column-header name="title_translated" sortable :sort-array="sortArray" :label="$t('Title')"></item-list-column-header>
        <item-list-column-header name="validation_rule" sortable :sort-array="sortArray" :label="$t('Validation rule')"></item-list-column-header>
        <item-list-column-header name="result_string_translated" sortable :sort-array="sortArray" :label="$t('Info for search results')"></item-list-column-header>
        <item-list-column-header name="modules" :label="$t('Modules')"></item-list-column-header>
    </template>

    <template slot="table-row" slot-scope="{ model, checkedModels, loading }">
        <td class="checkbox" v-if="$can('update taxonomies')||$can('delete taxonomies')"><item-list-checkbox :model="model" :checked-models-prop="checkedModels" :loading="loading"></item-list-checkbox></td>
        <td v-if="$can('update taxonomies')"><item-list-edit-button :url="'/admin/taxonomies/'+model.id+'/edit'"></item-list-edit-button></td>
        <td v-if="$can('update terms')">
            <a class="btn btn-secondary btn-xs" :href="'/admin/taxonomies/'+model.id+'/terms'">@lang('Terms')</a>
        </td>
        <td><item-list-position-input :model="model"></item-list-position-input></td>
        <td>@{{ model.name }}</td>
        <td v-html="model.title_translated"></td>
        <td><small class="text-muted">@{{ model.validation_rule }}</small></td>
        <td>@{{ model.result_string_translated }}</td>
        <td>
            <span class="badge bg-warning text-dark me-1" v-for="module in model.modules">@{{ module }}</span>
        </td>
    </template>

</item-list>

@endsection
