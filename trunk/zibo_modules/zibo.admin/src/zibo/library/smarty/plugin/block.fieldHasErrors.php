<?php

function smarty_block_fieldHasErrors($params, $content, &$smarty, &$repeat) {
    if ($repeat) {
        return;
    }

    if (!isset($params['form'])) {
        $form = $smarty->get_template_vars('block_form');
        if (!$form) {
            throw new Exception('No form parameter provided for the field');
        }
    } else {
        $form = $params['form'];
        unset($params['form']);
    }

    if (!empty($params['name'])) {
        $name = $params['name'];
    } else {
        $name = null;
    }

    $exception = $form->getValidationException();
    if ($exception != null && $exception->hasErrors($name)) {
        return $content;
    }

    return;
}