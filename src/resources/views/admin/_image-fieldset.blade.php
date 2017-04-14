<div ng-app="typicms" ng-cloak>
    @include('files::admin._filemanager', ['options' => ['dropzoneHidden', 'single', 'modal']])
    <div class="form-group">
        <label class="control-label">{{ __('Image') }}</label>
        {!! Bootform::hidden($field.'_id')->id($field.'_id') !!}

        <div ng-cloak ng-controller="FileController">
            <div ng-show="model.image" class="filemanager-item-removable">
                <a class="filemanager-item-removable-button" ng-click="removeImage(model)" href="#"><span class="fa fa-times"></span></a>
                <div class="filemanager-item-image-wrapper">
                    <img class="filemanager-item-image" ng-src="@{{ model.image.thumb_sm }}" alt="@{{ model.image.alt_attribute_translated }}">
                </div>
            </div>
            <button ng-hide="model.image" ng-click="openFilepicker()" class="btn btn-success add-attachment" type="button">{{ __('Add an image') }}</button>
        </div>
    </div>
</div>
