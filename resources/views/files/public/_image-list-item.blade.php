<li class="image-list-item">
    <a class="image-list-item-link" href="{{ $image->present()->image(1200, 1200, ['resize']) }}" data-pswp-width="{{ $image->width }}" data-pswp-height="{{ $image->height }}">
        <img class="image-list-item-image" src="{{ $image->present()->image(520) }}" width="{{ $image->width }}" height="{{ $image->height }}" alt="{{ $image->alt_attribute }}" />
    </a>
</li>
