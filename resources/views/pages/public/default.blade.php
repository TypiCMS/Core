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

        @if ($page->publishedSections->count() > 0)
        <div class="page-sections">
            @foreach ($page->publishedSections as $section)
            <div class="page-section" id="{{ $section->slug.'-'.$section->id }}">
                <h2 class="page-section-title">{{ $section->title }}</h2>
                <div class="rich-content">{!! $section->present()->body !!}</div>
            </div>
            @endforeach
        </div>
        @endif

    </div>

</div>

@endsection
