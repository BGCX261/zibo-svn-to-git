<?php

use zibo\library\i18n\I18n;

function smarty_function_fieldErrors($params, &$smarty) {
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
    $var = null;

    if (isset($params['var'])) {
        $var = $params['var'];
    }

    $exception = $form->getValidationException();
    if ($exception == null || !$exception->hasErrors($name)) {
        if ($var != null) {
            $smarty->assign($var, array());
        }
        return;
    }

    $translator = I18n::getInstance()->getTranslator();
    $errors = $exception->getErrors($name);

    if ($var != null) {
        $smarty->assign($var, $errors);
        return;
    }

    $html = '<ul class="errors">';
    foreach ($errors as $error) {
        $parameters = $error->getParameters();
        if (isset($parameters['value'])) {
            if (is_array($parameters['value'])) {
                $parameters['value'] = 'array';
            } elseif (is_object($parameters['value'])) {
                $parameters['value'] = 'object';
            }
        }

        $message = $translator->translate($error->getCode(), $parameters, $error->getMessage());
        $html .= '<li>' . $message . '</li>';
    }
    $html .= '</ul>';

    return $html;
}