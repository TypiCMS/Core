@if ($model->images->count() > 0)
    <ul class="image-list-list">
    @foreach ($model->images as $image)
        @include('files::public._image-list-item')
    @endforeach
    </ul>
@endif
