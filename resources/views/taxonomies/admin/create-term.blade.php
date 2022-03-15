@extends('core::admin.master')

@section('title', __('New term'))

@section('content')

    <div class="header">
        @include('core::admin._button-back', ['url' => route('admin::index-terms', $taxonomy)])
        <h1 class="header-title">@lang('New term')</h1>
    </div>

    {!! BootForm::open()->action(route('admin::index-terms', $taxonomy->id))->multipart()->role('form') !!}
        @include('taxonomies::admin._form-term')
    {!! BootForm::close() !!}

@endsection
