{!! BootForm::select(trans('validation.attributes.galleries'), 'galleries[]', Galleries::getModel()->lists('name', 'id'))->multiple(true)->id('galleries') !!}

{{-- Form::label('galleries', trans('validation.attributes.galleries'), array('class' => 'control-label')) --}}
{{-- Form::select(
    'galleries[]',
    $model->galleries->lists('name', 'id') + Galleries::getModel()->lists('name', 'id'),
    $model->galleries->lists('id'),
    array(
        'class' => 'form-control',
        'id' => 'galleries',
        'multiple'
    )
) --}}
