@extends('core::admin.master')

@section('title', __('New role'))

@section('content')

    {!! BootForm::open()->action(route('admin::index-roles'))->multipart()->role('form') !!}
        @include('roles::admin._form')
    {!! BootForm::close() !!}

@endsection
