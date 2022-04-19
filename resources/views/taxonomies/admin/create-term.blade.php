@extends('core::admin.master')

@section('title', __('New term'))

@section('content')

    {!! BootForm::open()->action(route('admin::index-terms', $taxonomy->id))->multipart()->role('form') !!}
        @include('taxonomies::admin._form-term')
    {!! BootForm::close() !!}

@endsection
