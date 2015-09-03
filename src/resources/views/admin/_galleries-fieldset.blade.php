{{--
We need an empty element to allow to deselect all items
and have a galleries key in the post data
--}}
<input type="hidden" name="galleries" value="">
{!! BootForm::select(
        trans('validation.attributes.galleries'),
        'galleries[]',
        $model->galleries->lists('name', 'id')->all() + Galleries::getModel()->lists('name', 'id')->all()
    )
    ->select($model->galleries->lists('id')->all())
    ->multiple(true)
    ->id('galleries')
!!}
