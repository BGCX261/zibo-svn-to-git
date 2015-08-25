<?php

use zibo\library\i18n\I18n;

function smarty_modifier_formatDate($string, $format = null) {
    $locale = I18n::getInstance()->getLocale();
    if ($format) {
        return $locale->formatDate($string, $format);
    }
    return $locale->formatDate($string);
}