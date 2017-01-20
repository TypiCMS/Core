{{--
We need an empty element to allow to deselect all items
and have a galleries key in the post data
--}}
{!! BootForm::hidden('galleries')->value('') !!}
{!! BootForm::select(
        __('validation.attributes.galleries'),
        'galleries[]',
        $model->galleries->pluck('name', 'id')->all() + Galleries::createModel()->pluck('name', 'id')->all()
    )
    ->select($model->galleries->pluck('id')->all())
    ->multiple(true)
    ->id('galleries')
!!}
