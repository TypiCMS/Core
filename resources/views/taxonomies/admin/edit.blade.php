@extends('core::admin.master')

@section('title', $model->title)

@section('content')

    {!! BootForm::open()->put()->action(route('admin::update-taxonomy', $model->id))->multipart()->role('form') !!}
    {!! BootForm::bind($model) !!}
        @include('taxonomies::admin._form')
    {!! BootForm::close() !!}

@endsection
