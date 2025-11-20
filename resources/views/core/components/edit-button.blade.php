@can('can:update page_sections')
    <div class="edit">
        <a class="edit-button" href="{{ route('admin::edit-page_section', [$section->page_id, $section->id]) }}">
            <span class="icon-pencil"></span>
            <span class="visually-hidden">{{ __('Edit') }}</span>
        </a>
    </div>
@endcan
