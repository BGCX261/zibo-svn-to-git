<?php

use zibo\library\html\form\field\OptionField;
use zibo\library\html\form\Form;

function smarty_function_field($params, &$smarty) {
    if (!isset($params['form'])) {
        $form = $smarty->get_template_vars('block_form');
        if (!$form) {
            throw new Exception('No form parameter provided for the field');
        }
    } else {
        $form = $params['form'];
        unset($params['form']);
    }

    if (isset($params['name'])) {
        $field = $form->getField($params['name']);
        unset($params['name']);
    } else {
        $field = $form->getField();
    }

    $option = null;
    if (isset($params['option'])) {
        $option = $params['option'];
        unset($params['option']);
    }

    foreach ($params as $key => $value) {
        $field->setAttribute($key, $value);
    }

    if ($option && $field instanceof OptionField) {
        $html = $field->getOptionHtml($option);
    } else {
        $html = $field->getHtml();
    }

    return $html;
}