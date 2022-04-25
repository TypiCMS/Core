@extends('core::admin.master')

@section('title', __('Users'))

@section('content')

<item-list
    url-base="/api/users"
    fields="id,first_name,last_name,email,activated,superuser,roles.name"
    table="users"
    title="users"
    include="roles"
    :translatable="false"
    :publishable="false"
    :exportable="true"
    :searchable="['first_name,last_name,email']"
    :sorting="['first_name']">

    <template slot="add-button" v-if="$can('create users')">
        @include('core::admin._button-create', ['module' => 'users'])
    </template>

    <template slot="columns" slot-scope="{ sortArray }">
        <item-list-column-header name="checkbox" v-if="$can('update users')||$can('delete users')"></item-list-column-header>
        <item-list-column-header name="edit" v-if="$can('update users')"></item-list-column-header>
        <item-list-column-header name="impersonate" v-if="$can('impersonate users')"></item-list-column-header>
        <item-list-column-header name="first_name" sortable :sort-array="sortArray" :label="$t('First name')"></item-list-column-header>
        <item-list-column-header name="last_name" sortable :sort-array="sortArray" :label="$t('Last name')"></item-list-column-header>
        <item-list-column-header name="email" sortable :sort-array="sortArray" :label="$t('Email')"></item-list-column-header>
        <item-list-column-header name="activated" sortable :sort-array="sortArray" :label="$t('Activated')"></item-list-column-header>
        <item-list-column-header name="role_names" :label="$t('Roles')"></item-list-column-header>
    </template>

    <template slot="table-row" slot-scope="{ model, checkedModels, loading }">
        <td class="checkbox" v-if="$can('update users')||$can('delete users')"><item-list-checkbox :model="model" :checked-models-prop="checkedModels" :loading="loading"></item-list-checkbox></td>
        <td v-if="$can('update users')"><item-list-edit-button :url="'/admin/users/'+model.id+'/edit'"></item-list-edit-button></td>
        <td v-if="$can('impersonate users')">
            <a class="btn-impersonate btn btn-link btn-sm text-secondary" title="Impersonate" onclick="if(!confirm('@lang('Impersonate this user?')'))return false" :href="'/admin/users/'+model.id+'/impersonate'"></a>
        </td>
        <td>@{{ model.first_name }}</td>
        <td>@{{ model.last_name }}</td>
        <td><a :href="'mailto:'+model.email">@{{ model.email }}</a></td>
        <td>
            <span class="badge bg-dark" v-if="model.activated">@lang('Yes')</span>
            <span class="badge bg-secondary text-body" v-else>@lang('No')</span>
        </td>
        <td>
            @if (auth()->user()->isSuperUser())
            <span class="badge bg-dark" v-if="model.superuser">Superuser</span>
            @endif
            <span class="badge bg-secondary text-body me-1" v-for="role in model.roles">@{{ role.name }}</span>
        </td>
    </template>

</item-list>

@endsection
