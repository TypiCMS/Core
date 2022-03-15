@extends('core::admin.master')

@section('title', __('Users'))

@section('content')

<item-list
    url-base="/api/users"
    locale="{{ config('typicms.content_locale') }}"
    fields="id,first_name,last_name,email,activated,superuser,roles.name"
    table="users"
    title="users"
    include="roles"
    :multilingual="false"
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
        <item-list-column-header name="first_name" sortable :sort-array="sortArray" :label="$t('First name')"></item-list-column-header>
        <item-list-column-header name="last_name" sortable :sort-array="sortArray" :label="$t('Last name')"></item-list-column-header>
        <item-list-column-header name="email" sortable :sort-array="sortArray" :label="$t('Email')"></item-list-column-header>
        <item-list-column-header name="activated" sortable :sort-array="sortArray" :label="$t('Activated')"></item-list-column-header>
        <item-list-column-header name="role_names" :label="$t('Roles')"></item-list-column-header>
    </template>

    <template slot="table-row" slot-scope="{ model, checkedModels, loading }">
        <td class="checkbox" v-if="$can('update users')||$can('delete users')"><item-list-checkbox :model="model" :checked-models-prop="checkedModels" :loading="loading"></item-list-checkbox></td>
        <td v-if="$can('update users')">@include('core::admin._button-edit', ['module' => 'users'])</td>
        <td>@{{ model.first_name }}</td>
        <td>@{{ model.last_name }}</td>
        <td><a :href="'mailto:'+model.email">@{{ model.email }}</a></td>
        <td>
            <span class="badge bg-secondary" v-if="model.activated">@lang('Yes')</span>
            <span class="badge bg-light text-dark" v-else>@lang('No')</span>
        </td>
        <td>
            @if (auth()->user()->isSuperUser())
            <span class="badge bg-secondary" v-if="model.superuser">Superuser</span>
            @endif
            <span class="badge bg-light text-dark me-1" v-for="role in model.roles">@{{ role.name }}</span>
        </td>
    </template>

</item-list>

@endsection
