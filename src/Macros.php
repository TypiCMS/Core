<?php
/**
 * Back office buttons in view list
 */
HTML::macro('langButton', function ($locale = null, $attributes = []) {

    $inputs = Input::except('locale');
    $inputs['locale'] = $locale;

    $attributes['class'] = 'btn btn-default btn-xs';
    if ($locale == Config::get('app.locale')) {
        $attributes['class'] .= ' active';
    }
    $label = trans('global.languages.' . $locale);
    $attributes['href'] = '?' . http_build_query($inputs);

    return '<a ' . HTML::attributes($attributes) . '>' . $label . '</a>';

});

/**
 * Front office lang switcher
 */
HTML::macro('languagesMenu', function (array $langsArray = array(), array $attributes = array()) {

    $attributes['role'] = 'menu';
    $attributes = HTML::attributes($attributes);

    $html = array();
    $html[] = '<ul ' . $attributes . '>';
    foreach ($langsArray as $item) {
        $html[] = '<li class="' . $item->class . '" role="menuitem">';
        $html[] = '<a href="' . url($item->url) . '">' . $item->lang . '</a>';
        $html[] = '</li>';
    }
    $html[] = '</ul>';

    return implode("\r\n", $html);

});
