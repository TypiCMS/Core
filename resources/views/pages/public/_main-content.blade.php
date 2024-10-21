<div class="page-row">
    @empty($page->image)
        <div class="page-content">
            @empty(!$page->body)
                <div class="rich-content">{!! $page->present()->body !!}</div>
            @endempty
        </div>
    @else
        <div class="page-left">
            @empty(!$page->body)
                <div class="rich-content">{!! $page->present()->body !!}</div>
            @endempty
        </div>
        <div class="page-right">
            <figure class="page-figure">
                <img class="page-figure-image" src="{{ $page->present()->image(2400) }}" width="{{ $page->image->width }}" height="{{ $page->image->height }}" alt="{{ $page->image->alt_attribute }}" />
                @empty(!$page->image->description)
                    <figcaption class="page-figure-caption">{{ $page->image->description }}</figcaption>
                @endempty
            </figure>
        </div>
    @endempty
</div>
