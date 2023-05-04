<li class="document-list-item">
    <a class="document-list-item-link" href="{{ Storage::url($document->path) }}" target="_blank" rel="noopener noreferrer">
        <i class="document-list-item-icon bi bi-file-earmark-fill"></i>
        <div class="document-list-item-info">
            <span class="document-list-item-filename">{{ $document->present()->title }}</span>
            <div class="document-list-item-meta">
                <small class="document-list-item-meta-extension">{{ $document->extension }}</small>
                <small class="document-list-item-meta-filesize">{{ $document->present()->filesize }}</small>
            </div>
        </div>
    </a>
</li>
