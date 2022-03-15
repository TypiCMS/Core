@extends('core::admin.master')

@section('title', $model->title)

@section('content')

    <div class="header">
        @include('core::admin._button-back', ['url' => route('admin::index-terms', $taxonomy)])
        <h1 class="header-title @if (!$model->title)text-muted @endif">
            {{ $model->title ?: __('Untitled') }}
        </h1>
    </div>

    {!! BootForm::open()->put()->action(route('admin::update-term', [$taxonomy->id, $model->id]))->multipart()->role('form') !!}
    {!! BootForm::bind($model) !!}
        @include('taxonomies::admin._form-term')
    {!! BootForm::close() !!}

@endsection
