@if ($subpages = $page->getSubMenu() and !empty($subpages))
<ul class="page-header-subpages">
    @foreach ($subpages as $subpage)
    <li><a class="page-header-subpages-link {{ $page->id === $subpage->id ? 'page-header-subpages-link-active' : '' }}" href="{{ url($subpage->uri()) }}">{{ $subpage->title }}</a></li>
    @endforeach
</ul>
@endif
