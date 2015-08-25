<?php

function smarty_function_fieldLocales($params, &$smarty) {
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

    if (empty($params['locales']) || !is_array($params['locales'])) {
        throw new Exception('No locales (array) parameter provided for the field');
    }
    $locales = $params['locales'];

    $id = $form->getId() . ucfirst($name) . 'Locales';

    $html = '<ul id="' . $id . '" class="locales">';
    foreach ($locales as $locale => $data) {
        if ($data === null) {
            $localeString = $locale;
            $fieldString = '---';
        } else {
            $localeString = '<strong>' . $locale . '</strong>';
            if ($data === $locale) {
                $fieldString = null;
            } else {
                $fieldString = $data->$name;
            }
        }

        $html .= '<li>' . $localeString . ($fieldString ? ': ' : '') . $fieldString . '</li>';
    }
    $html .= '</ul>';

    return $html;
}