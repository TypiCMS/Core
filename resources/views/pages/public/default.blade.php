@extends('pages::public.master')

@section('page')

<div class="page-body">

    <div class="page-body-container">

        @if ($children->count() > 0)
        <ul class="nav nav-subpages">
            @foreach ($children as $child)
            @include('pages::public._list-item', ['child' => $child])
            @endforeach
        </ul>
        @endif

        @empty(!$page->image)
            <img class="page-image" src="{{ $page->present()->image(2000) }}" width="{{ $page->image->width }}" height="{{ $page->image->height }}" alt="">
        @endempty

        @empty(!$page->body)
        <div class="rich-content">{!! $page->present()->body !!}</div>
        @endempty

        @include('files::public._documents', ['model' => $page])
        @include('files::public._images', ['model' => $page])

    </div>

    @include('pages::public._sections', ['model' => $page])

</div>

@endsection
