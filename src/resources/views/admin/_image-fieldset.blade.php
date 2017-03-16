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
            <div ng-show="model.image" class="filemanager-item-removable">
                <a class="filemanager-item-removable-button" ng-click="removeImage(model)" href="#"><span class="fa fa-times"></span></a>
                <div class="filemanager-item-image-wrapper">
                    <img class="filemanager-item-image" ng-src="@{{ model.image.thumb_sm }}" alt="@{{ model.image.alt_attribute_translated }}">
                </div>
            </div>
            <button ng-hide="model.image" ng-click="addImage(model)" class="btn btn-success add-attachment" type="button">{{ __('Add an image') }}</button>
        </div>
    </div>
</div>
