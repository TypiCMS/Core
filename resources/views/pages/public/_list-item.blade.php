<li class="page-list-item {{ Request::is($child->url()) ? 'active' : '' }}" id="page_{{ $child->id }}">
    <a class="page-list-item-link" href="{{ $child->url() }}">
        {{ $child->title }}
    </a>
    @if ($child->items)
        <ul class="page-list-item-children">
            @foreach ($child->items as $childPage)
                @include('pages::public._list-item', ['child' => $childPage])
            @endforeach
        </ul>
    @endif
</li>
