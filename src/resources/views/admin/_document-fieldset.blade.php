<div ng-cloak>
    @include('files::admin._filemanager', ['options' => ['no-dropzone', 'single', 'modal']])
    <div class="form-group">
        <label class="control-label">{{ __('Document') }}</label>
        {!! Bootform::hidden('document_id')->id('document_id') !!}

        <div ng-cloak ng-controller="DocumentController">
            <div ng-show="model.document" class="filemanager-item-removable">
                <div class="filemanager-item-wrapper" style="padding-top: 7px; padding-right: 40px;">
                    <span class="fa fa-file-o"></span> @{{ model.document.name }} <a style="display: flex;" class="filemanager-item-removable-button" ng-click="removeDocument(model)" href="#"><span class="fa fa-times"></span></a>
                </div>
            </div>
            <button ng-hide="model.document" ng-click="openFilepicker()" class="btn btn-success add-attachment" type="button">{{ __('Add a document') }}</button>
        </div>
    </div>
</div>
