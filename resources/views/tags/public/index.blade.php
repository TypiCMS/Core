@extends('pages::public.master')

@section('bodyClass', 'body-tags body-tags-index body-page body-page-'.$page->id)

@section('page')

<div class="page-body">

    <div class="page-body-container">

        <div class="rich-content">{!! $page->present()->body !!}</div>

        @include('files::public._documents', ['model' => $page])
        @include('files::public._images', ['model' => $page])


        @if ($models->count() > 0)
        @include('tags::public._list', ['items' => $models])
        @endif

        {!! $models->appends(Request::except('page'))->links() !!}

    </div>

</div>

@endsection
