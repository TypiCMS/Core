<div class="section-default section-{{ $section->id }}" id="{{ $section->slug }}-{{ $section->id }}">
    <div class="section-default-container">
        <div class="section-default-row">
            @empty($section->image)
                <div class="section-default-content">
                    <h2 class="section-default-title">{{ $section->title }}</h2>
                    <div class="section-default-text rich-content">{!! $section->present()->body !!}</div>
                </div>
            @else
                <div class="section-default-left">
                    <h2 class="section-default-title">{{ $section->title }}</h2>
                    <div class="section-default-text rich-content">{!! $section->present()->body !!}</div>
                </div>
                <div class="section-default-right">
                    <figure class="section-default-figure">
                        <img
                            class="section-default-figure-image"
                            src="{{ $section->present()->image(990) }}"
                            width="{{ $section->image->width }}"
                            height="{{ $section->image->height }}"
                            alt="{{ $section->image->alt_attribute }}"
                        />
                        @empty(! $section->image->description)
                            <figcaption class="section-default-figure-caption">
                                {{ $section->image->description }}
                            </figcaption>
                        @endempty
                    </figure>
                </div>
            @endempty
        </div>
        @include('files::public._document-list', ['model' => $section])
        @include('files::public._image-list', ['model' => $section])
    </div>
</div>
