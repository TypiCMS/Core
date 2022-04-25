<div class="section-default section-{{ $section->id }}" id="section-{{ $section->id }}">
    <div class="section-default-container">
        @empty ($section->image)
        <div class="section-default-content">
            <h2 class="section-default-title">{{ $section->title }}</h2>
            <div class="section-default-text rich-content">{!! $section->present()->body !!}</div>
        </div>
        @else
        <div class="section-default-row">
            <div class="section-default-image">
                <figure class="section-default-image-figure">
                    <img class="section-default-image-img" src="{{ $section->present()->image(1136, 940) }}" width="1136" height="940" alt="{{ $section->image->alt_attribute }}">
                    <figcaption class="section-default-image-figcaption">{!! nl2br($section->image->description) !!}</figcaption>
                </figure>
            </div>
            <div class="section-default-content">
                <h2 class="section-default-title">{{ $section->title }}</h2>
                <div class="section-default-text rich-content">{!! $section->present()->body !!}</div>
            </div>
        </div>
        @endempty
        @include('files::public._documents', ['model' => $section])
        @include('files::public._images', ['model' => $section])
    </div>
</div>
