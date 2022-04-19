@extends('core::admin.master')

@section('title', __('New tag'))

@section('content')

    {!! BootForm::open()->action(route('admin::index-tags'))->multipart()->role('form') !!}
        @include('tags::admin._form')
    {!! BootForm::close() !!}

@endsection
