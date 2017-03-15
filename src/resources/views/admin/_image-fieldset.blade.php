<div ng-app="typicms">
    <div class="filepicker" id="filepicker">
        <div class="filepicker-content">
            @include('files::admin._filemanager', ['options' => ['dropzoneHidden', 'addFileButton']])
        </div>
    </div>
    <div class="form-group">
        <label class="control-label">{{ __('Image') }}</label>
        {!! Bootform::hidden($field.'_id')->id($field.'_id') !!}

        <div ng-app="typicms" ng-cloak ng-controller="FileController">
            <div ng-show="model.image" class="filemanager-item-wrapper">
                <div class="filemanager-item-icon">
                    <img class="filemanager-item-image" ng-src="@{{ model.image.thumb_sm }}" alt="@{{ model.image.alt_attribute_translated }}">
                </div>
                <button class="btn btn-xs btn-danger delete-attachment" ng-click="removeImage(model)" data-message="{{ __('Delete this image?') }}" data-field="{{ $field }}_id" type="button">{{ __('Delete') }}</button>
                <button class="btn btn-xs btn-default replace-attachment" ng-click="addImage(model)" type="button">{{ __('Replace') }}</button>
            </div>
            <button ng-hide="model.image" ng-click="addImage(model)" class="btn btn-success add-attachment" type="button">{{ __('Add an image') }}</button>
        </div>
    </div>
</div>
