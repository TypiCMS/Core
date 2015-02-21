{!! BootForm::select(
        trans('validation.attributes.galleries'),
        'galleries[]',
        $model->galleries->lists('name', 'id') + Galleries::getModel()->lists('name', 'id')
    )
    ->select($model->galleries->lists('id'))
    ->multiple(true)
    ->id('galleries')
!!}
