        <div class="fieldset-media fieldset-image">
            @if($model->$field)
            <div class="fieldset-preview">
                {!! $model->present()->thumb(150, 150, ['resize'], $field) !!}
                <small class="text-danger delete-attachment" data-table="{{ $model->getTable() }}" data-id="{{ $model->id }}" data-field="{{ $field }}">@lang('global.Delete')</small>
            </div>
            @endif
            <div class="fieldset-field">
                {!! BootForm::file(trans('validation.attributes.'.$field), $field) !!}
            </div>
        </div>
