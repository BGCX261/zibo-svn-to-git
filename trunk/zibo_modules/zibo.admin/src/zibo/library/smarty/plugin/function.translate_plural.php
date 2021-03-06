<?php

use zibo\library\i18n\I18n;

function smarty_function_translate_plural($params, &$smarty) {
    if (empty($params['key'])) {
        throw new Exception('No key found to translate');
    }

    if (empty($params['n']) && $params['n'] !== 0) {
        throw new Exception('No plural count "n" found to translate');
    }

    $key = $params['key'];
    unset($params['key']);

    $var = null;
    if (!empty($params['var'])) {
        $var = $params['var'];
        unset($params['var']);
    }

    $n = $params['n'];

    $translator = I18n::getInstance()->getTranslator();
    $translation = $translator->translatePlural($n, $key, $params, null);

    if ($var == null) {
        return $translation;
    } else {
        $smarty->assign($var, $translation);
    }
}
