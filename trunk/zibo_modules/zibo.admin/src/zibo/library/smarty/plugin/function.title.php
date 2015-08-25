<?php

function smarty_function_title($params, &$smarty) {
    $baseUrl = $smarty->get_template_vars('_baseUrl');
    $title = $smarty->get_template_vars('_title');

    if (!$title) {
        return '';
    }

    $attributes = '';
    foreach ($params as $key => $value) {
        $attributes .= ' ' . $key . '="' . $value . '"';
    }

    return '<h1' . $attributes . '><a href="' . $baseUrl . '">' . $title . '</a></h1>';
}