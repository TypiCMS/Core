<div class="filepicker" id="filepicker">
    <div class="filepicker-content">
        @include('files::admin._filemanager', ['options' => ['dropzoneHidden', 'addButton']])
    </div>
</div>
<div class="form-group">
    <label class="control-label">Image</label>
    @if($model->$field)
    <div class="filemanager-item-wrapper">
        <div class="filemanager-item-icon">
            <img class="filemanager-item-image" src="{{ $model->$field->thumb_sm }}" alt="{{ $model->$field->alt_attribute_translated }}">
        </div>
        <button class="btn btn-xs btn-danger delete-attachment" data-table="{{ $model->getTable() }}" data-id="{{ $model->id }}" data-field="{{ $field }}" type="button">{{ __('Delete') }}</button>
        <button class="btn btn-xs btn-default replace-attachment" data-table="{{ $model->getTable() }}" data-id="{{ $model->id }}" data-field="{{ $field }}" type="button">{{ __('Replace') }}</button>
    </div>
    @else
    <p>
        <button class="btn btn-success add-attachment" id="select-files" data-table="{{ $model->getTable() }}" data-id="{{ $model->id }}" data-field="{{ $field }}" type="button">{{ __('Add image') }}</button>
    </p>
    @endif
</div>
