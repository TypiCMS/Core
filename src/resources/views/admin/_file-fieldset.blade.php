        <div class="fieldset-media fieldset-file">
            @if($model->$field)
            <div class="fieldset-preview">
                @if ($model->type == 'i')
                    {!! $model->present()->thumb(150, 150, ['resize'], $field) !!}
                @else
                    {!! $model->present()->icon(2, $field) !!}
                @endif
                <small class="text-danger delete-attachment" data-table="{{ $model->getTable() }}" data-id="{{ $model->id }}" data-field="{{ $field }}">Supprimer</small>
            </div>
            @endif
            <div class="fieldset-field">
                {!! BootForm::file(__('validation.attributes.'.$field), $field) !!}
            </div>
        </div>
