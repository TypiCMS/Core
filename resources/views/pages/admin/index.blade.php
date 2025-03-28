@extends('core::admin.master')

@section('title', __('Pages'))

@section('content')
    <item-list-tree url-base="/api/pages" fields="id,position,parent_id,module,redirect,is_home,private,status,title,slug,uri" table="pages" title="Pages">
        <template #add-button v-if="$can('create pages')">
            @include('core::admin._button-create', [
                'url' => route('admin::create-page'),
                'module' => 'pages',
            ])
        </template>
    </item-list-tree>
@endsection
