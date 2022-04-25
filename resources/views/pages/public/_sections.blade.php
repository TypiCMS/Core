@if ($model->publishedSections->count() > 0)
<div class="page-sections">
    @foreach ($model->publishedSections as $section)
        @include('pages::public._section-'.($section->template ?? 'default'))
    @endforeach
</div>
@endif
