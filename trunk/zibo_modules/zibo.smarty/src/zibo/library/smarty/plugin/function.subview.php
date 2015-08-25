<?php

/**
 * Smarty function to display a subview
 * @param array $params
 * @param Smarty $smarty
 * @return string the HTML of a assigned subview
 */
function smarty_function_subview($params, &$smarty) {
    if (empty($params['name'])) {
        throw new Exception('No name parameter provided for the subview');
    }

    $name = $params['name'];
    $views = $smarty->get_template_vars('_views');

    if (!isset($views[$name]['html'])) {
        return;
    }

    return $views[$name]['html'];
}