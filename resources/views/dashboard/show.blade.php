@extends('core::admin.master')

@section('title', __('Dashboard'))

@section('content')
    <div class="header pt-4">
        <h1 class="card-title mb-3">@lang('Welcome, :name!', ['name' => e(auth()->user()->first_name)])</h1>
        <div class="card-text">{!! $welcomeMessage !!}</div>
    </div>
    <div class="content">
        @can('see history')
            <history fields="history.id,history.created_at,history.title,history.locale,history.historable_id,history.historable_type,history.action,history.user_id" include="historable" :searchable="['title,historable_type,action,user_name']" :sorting="['-created_at']" @can('clear history'):clear-button="true"@endcan>
                <template #columns="{ sortArray }">
                    <item-list-column-header name="created_at" sortable :sort-array="sortArray" :label="$t('Date')"></item-list-column-header>
                    <item-list-column-header name="title" sortable :sort-array="sortArray" :label="$t('Title')"></item-list-column-header>
                    <item-list-column-header name="historable_type" sortable :sort-array="sortArray" :label="$t('Module')"></item-list-column-header>
                    <item-list-column-header name="action" sortable :sort-array="sortArray" :label="$t('Action')"></item-list-column-header>
                    <item-list-column-header name="user_name" sortable :sort-array="sortArray" :label="$t('User')"></item-list-column-header>
                </template>
            </history>
        @endcan
    </div>
@endsection
