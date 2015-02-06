        <div class="clearfix well media @if($errors->has($field))has-error @endif">
            @if($model->$field)
                {!! $model->present()->icon(2, $field) !!}
            @endif
            <div>
                {!! BootForm::file(trans('validation.attributes.' . $field), $field) !!}
                <span class="help-block">
                    @lang('validation.attributes.max :size MB', array('size' => 2))
                </span>
                {!! $errors->first($field, '<p class="help-block">:message</p>') !!}
            </div>
        </div>
