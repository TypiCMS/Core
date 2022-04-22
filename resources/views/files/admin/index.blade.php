@extends('core::admin.master')

@section('title', __('Files'))

@section('content')

<file-manager :modal="false" locale="{{ config('typicms.content_locale') }}"></file-manager>

@endsection
