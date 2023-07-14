@if ($model->images->count() > 0)
    <ul class="image-list-list" id="image-list-list">
        @foreach ($model->images as $image)
            @include('files::public._image-list-item')
        @endforeach
    </ul>
@endif

@push('js')
    <script type="module">
        import PhotoSwipeLightbox from '/components/photoswipe/photoswipe-lightbox.esm.min.js';

        const lightbox = new PhotoSwipeLightbox({
            gallery: '#image-list-list',
            children: 'a',
            pswpModule: () => import('/components/photoswipe/photoswipe.esm.min.js'),
        });
        lightbox.init();
    </script>
@endpush
