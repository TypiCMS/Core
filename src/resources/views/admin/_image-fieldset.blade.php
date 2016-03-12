        <div class="fieldset-media fieldset-image">
            <div class="fieldset-preview">
                @if($model->$field)
                    {!! $model->present()->thumb(150, 150, ['resize'], $field) !!}
                    <small class="text-danger delete-attachment" data-table="{{ $model->getTable() }}" data-id="{{ $model->id }}" data-field="{{ $field }}">Delete</small>
                @endif
            </div>
            <div class="fieldset-field">
                {!! BootForm::text(trans('validation.attributes.' . $field), $field)->class('form-control image-selector') !!}
            </div>
            <a href="" class="popup_selector" data-inputid="{{$field}}">Select Image</a>
        </div>
