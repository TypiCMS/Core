@if ($subpages = $page->getSubPages() and !empty($subpages))
<ul class="subpages">
    @foreach ($subpages as $child)
    @include('pages::public._list-item', compact('child'))
    @endforeach
</ul>
@endif
