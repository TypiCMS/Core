<li class="image-list-item">
    <a class="image-list-item-link"
        href="{{ $image->present()->image(1200, 1200, ['resize']) }}"
        data-caption="{{ $image->present()->title }}"
        data-fancybox="{{ $model->slug ? : 'group' }}"
        data-options='{ "buttons": ["close"], "infobar": false }'
    >
        <img class="image-list-item-image" src="{{ $image->present()->image(520, 520) }}" width="260" height="260" alt="{{ $image->alt_attribute }}">
    </a>
</li>
