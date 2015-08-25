<?php

use zibo\library\i18n\I18n;

function smarty_function_translate($params, &$smarty) {
    if (empty($params['key'])) {
        throw new Exception('No key found to translate');
    }
    $key = $params['key'];
    unset($params['key']);

    $var = null;
    if (!empty($params['var'])) {
        $var = $params['var'];
        unset($params['var']);
    }

    $translator = I18n::getInstance()->getTranslator();
    $translation = $translator->translate($key, $params);

    if ($var == null) {
        return $translation;
    } else {
        $smarty->assign($var, $translation);
    }

}