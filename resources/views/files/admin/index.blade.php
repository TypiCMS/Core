@extends('core::admin.master')

@section('title', __('Files'))

@section('content')

<file-manager :modal="false"></file-manager>

@endsection
