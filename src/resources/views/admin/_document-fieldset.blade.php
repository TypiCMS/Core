        <div class="fieldset-media fieldset-document">
            @if ($model->$field)
            <div class="fieldset-preview">
                {!! $model->present()->icon(2, $field) !!}
                <small class="text-danger delete-attachment" data-table="{{ $model->getTable() }}" data-id="{{ $model->id }}" data-field="{{ $field }}">Supprimer</small>
            </div>
            @endif
            <div class="fieldset-field">
                {!! BootForm::file(__(''.$field), $field) !!}
            </div>
        </div>
