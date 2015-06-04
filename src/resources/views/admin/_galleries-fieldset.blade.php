{!! BootForm::select(
        trans('validation.attributes.galleries'),
        'galleries[]',
        $model->galleries->lists('name', 'id')->all() + Galleries::getModel()->lists('name', 'id')->all()
    )
    ->select($model->galleries->lists('id'))
    ->multiple(true)
    ->id('galleries')
!!}
