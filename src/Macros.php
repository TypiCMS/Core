<?php
/**
 * Back office buttons in view list
 */
HTML::macro('langButton', function ($locale = null, $attributes = []) {

    $inputs = Input::except('locale');
    $inputs['locale'] = $locale;

    $attributes['class'] = 'btn btn-default btn-xs';
    if ($locale == config('app.locale')) {
        $attributes['class'] .= ' active';
    }
    $label = trans('global.languages.' . $locale);
    $attributes['href'] = '?' . http_build_query($inputs);

    return '<a ' . HTML::attributes($attributes) . '>' . $label . '</a>';

});
