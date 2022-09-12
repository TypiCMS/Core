@extends('pages::public.master')

@section('page')

<div class="page-body">

    <div class="page-body-container">

        @include('pages::public._subpages')
        @include('pages::public._main-content', ['page' => $page])
        @include('files::public._documents', ['model' => $page])
        @include('files::public._images', ['model' => $page])

    </div>

    @include('pages::public._sections', compact('page'))

</div>

@endsection
