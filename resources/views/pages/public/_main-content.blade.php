@empty(!$page->image)
    <div class="page-row">
        <div class="page-left">
            <h1 class="page-title">{!! nl2br($page->title) !!}</h1>
            @empty($page->body)
                <div class="rich-content">{!! $page->present()->body !!}</div>
            @endempty
        </div>
        <div class="page-right">
            <figure class="page-figure">
                <img class="page-figure-image mb-5" src="{{ $page->present()->image(2400) }}" width="{{ $page->image->width }}" height="{{ $page->image->height }}" alt="{{ $page->image->alt_attribute }}">
                @empty(!$page->image->description)
                <figcaption class="page-figure-caption">{{ $page->image->description }}</figcaption>
                @endempty
            </figure>
        </div>
    </div>
@else
    <h1 class="page-title">{!! nl2br($page->title) !!}</h1>
    @empty(!$page->body)
        <div class="rich-content">{!! $page->present()->body !!}</div>
    @endempty
@endempty
