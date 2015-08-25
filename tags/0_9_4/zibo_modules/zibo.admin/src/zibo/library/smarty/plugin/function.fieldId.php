<?php

use zibo\library\html\form\Form;

function smarty_function_fieldId($params, &$smarty) {
    if (!isset($params['form'])) {
        $form = $smarty->get_template_vars('block_form');
        if (!$form) {
            throw new Exception('No form parameter provided for the field');
        }
    } else {
        $form = $params['form'];
        unset($params['form']);
    }

    if (empty($params['name'])) {
        throw new Exception('No name parameter provided for the field');
    }
    $name = $params['name'];

    $field = $form->getField($name);

    if (empty($params['var'])) {
        return $field->getId();
    } else {
        $smarty->assign($params['var'], $field->getId());
    }
}