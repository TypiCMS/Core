<li class="document-list-item">
    <a class="document-list-item-link" href="{{ Storage::url($document->path) }}" target="_blank" rel="noopener noreferrer">
        <svg class="document-list-item-icon" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0H4zm5.5 1.5v2a1 1 0 0 0 1 1h2l-3-3z"/>
        </svg>
        <span class="document-list-item-filename">{{ $document->present()->title }}</span> <small class="document-list-item-filesize">{{ $document->present()->filesize }}</small>
    </a>
</li>
