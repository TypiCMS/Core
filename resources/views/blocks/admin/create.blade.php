@extends('core::admin.master')

@section('title', __('New content block'))

@section('content')

    {!! BootForm::open()->action(route('admin::index-blocks'))->multipart()->role('form') !!}
        @include('blocks::admin._form')
    {!! BootForm::close() !!}

@endsection
