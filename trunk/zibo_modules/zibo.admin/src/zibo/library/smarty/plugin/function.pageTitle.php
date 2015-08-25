<?php

function smarty_function_pageTitle($params, &$smarty) {
    $pageTitle = $smarty->get_template_vars('_pageTitle');

    if (!$pageTitle) {
        return '';
    }

    $attributes = '';
    foreach ($params as $key => $value) {
        $attributes .= ' ' . $key . '="' . $value . '"';
    }

    return '<h2' . $attributes . '>' . $pageTitle . '</h2>';
}