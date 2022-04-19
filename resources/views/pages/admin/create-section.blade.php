@extends('core::admin.master')

@section('title', __('New page section'))

@section('content')

    {!! BootForm::open()->action(route('admin::store-page_section', $page->id))->multipart()->role('form') !!}
        @include('pages::admin._form-section')
    {!! BootForm::close() !!}

@endsection
